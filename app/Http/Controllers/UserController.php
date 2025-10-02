<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pustu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserImport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        $query = DB::table('users')
            ->select(
                'users.*',
                'pustus.nama_pustu as nama_pustu',
                'districts.name as nama_district',
                'regencies.name as nama_regency',
                DB::raw('COALESCE(regencies.name, regencies_user.name) as nama_regency'),
            )
            ->leftJoin('pustus', 'users.pustu_id', '=', 'pustus.id')
            //->leftJoin('villages', 'pustus.village_id', '=', 'villages.id')
            ->leftJoin('districts', 'pustus.district_id', '=', 'districts.id')
            ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')
            ->whereNull('users.deleted_at')
            ->leftJoin('regencies as regencies_user', 'users.regency_id', '=', 'regencies_user.id');

        if ($currentUser->role === 'sudinkes') {
            $query->where(function ($q) use ($currentUser) {
                $q->where('regencies.id', $currentUser->regency_id)
                  ->orWhere(function ($q2) use ($currentUser) {
                      $q2->where('users.role', 'sudinkes')
                         ->where('users.regency_id', $currentUser->regency_id);
                  });
            });
        } elseif ($currentUser->role !== 'superadmin') {
            $query->where('users.pustu_id', $currentUser->pustu_id);
        }

        if ($request->role) {
            $query->where('users.role', $request->role);
        }

        $users = $query->orderBy('users.created_at', 'asc')->get();


        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $currentUser = Auth::user();

        if(Auth::user()->role=='sudinkes'){
            $pustus = \App\Models\Pustu::select('pustus.nama_pustu','pustus.id')->join('villages','villages.id','pustus.village_id')
            ->join('districts','districts.id','villages.district_id')
            ->join('regencies','regencies.id','districts.regency_id')
            ->where('regencies.id',Auth::user()->regency_id)
            ->get();
        }else{
            $pustus = \App\Models\Pustu::all();
        }
        $parents = collect();

        // If user is superadmin, get potential parents for dropdown
        if ($currentUser->role == 'superadmin') {
            // Get all facilities that can be parents
            $parents = User::whereIn('role', ['puskesmas', 'pustu', 'klinik'])
                           ->get();
        }

        return view('users.create', compact('parents','pustus'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // Validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string'],
            'no_wa' => ['string', 'max:255'],
            'keterangan' => ['string', 'max:255', 'nullable'],
        ];



        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->route('users.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Determine pustu_id
        $parentId = null;

        // If superadmin and pustu_id provided, use it
        if ($currentUser->role == 'superadmin' && $request->has('pustu_id') && !empty($request->pustu_id)) {
            $parentId = $request->pustu_id;
        }
        // If not superadmin, use current user as parent
        elseif ($currentUser->role != 'superadmin') {
            $parentId = $currentUser->id;
        }

        // Role access validation
        //$this->validateRoleAccess($currentUser->role, $request->role);

        //return $request->all();
        // Create the user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'pustu_id' => ($request->role=='perawat' || $request->role=='operator')?$request->pustu_id:null,
            'no_wa' => $request->no_wa,
            'regency_id'=>$request->role=='sudinkes'?$request->regency_id:null,
            'keterangan' => $request->keterangan,
            'status_pegawai' => $request->status_pegawai
        ]);

        return redirect('users?role=perawat')
            ->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->authorizeAccess($user);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        //$this->authorizeAccess($user);

        $currentUser = Auth::user();
        $parents = collect();
        $pustus = \App\Models\Pustu::all();
        // If user is superadmin, get potential parents for dropdown
        if ($currentUser->role == 'superadmin') {
            $parents = User::whereIn('role', ['puskesmas', 'pustu', 'klinik'])
                           ->get();
        }

        return view('users.edit', compact('user', 'parents', 'pustus'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        //$this->authorizeAccess($user);
        
        $currentUser = Auth::user();
        // Validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'no_wa' => ['string', 'max:255'],
            'pustu_id' => ['nullable', 'exists:pustus,id'],
            'keterangan' => ['string', 'max:255', 'nullable'],
        ];

        // Password is optional during update
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        // Additional validation for pustu_id when needed
        if ($currentUser->role == 'superadmin' && in_array($request->role, ['pustu', 'dokter', 'perawat', 'operator', 'farmasi', 'pendaftaran'])) {
            $rules['pustu_id'] = ['required', 'exists:users,id'];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->route('users.edit', $user)
                ->withErrors($validator)
                ->withInput();
        }

        // Determine pustu_id
        $parentId = $user->pustu_id;

        // If superadmin and pustu_id provided, use it
        if ($currentUser->role == 'superadmin' && $request->has('pustu_id') && !empty($request->pustu_id)) {
            $parentId = $request->pustu_id;
        }

        // Role access validation
        //$this->validateRoleAccess($currentUser->role, $request->role);

        $regencyId = $user->regency_id; // default
        if (!empty($parentId)) {
            $pustu = Pustu::with('districts.regency')->find($parentId);
            if ($pustu && $pustu->districts && $pustu->districts->regency) {
                $regencyId = $pustu->districts->regency->id;
            }
        }
        // Update user data
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            //'role' => $request->role,
            'pustu_id' => $parentId ?? $request->pustu_id,
            'no_wa' => $request->no_wa,
            'keterangan' => $request->keterangan,
            //'village' => $request->village,
            //'district' => $request->district,
            'regency_id' => $regencyId,
            'status_pegawai' => $request->status_pegawai
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()
            ->route('users.index' , ['role' => auth()->user()->role])
            ->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->authorizeAccess($user);
        $role = $user->role;
        // Cek apakah user punya child
        $hasChildren = User::where('pustu_id', $user->id)->exists();

        if ($hasChildren) {
            return redirect()
                ->route('users.index', ['role' => $role])
                ->with('error', 'User tidak dapat dihapus karena masih memiliki user-user di bawahnya!');
        }

        // Soft delete
        if ($user->delete()) {
            return redirect()
            ->route('users.index', ['role' => $role])
            ->with('success', 'User berhasil dihapus!');
        }

        return redirect()
            ->route('users.index', ['role' => $role])
            ->with('error', 'Terjadi kesalahan saat menghapus user.');
    }

    /**
     * Validate if current user has access to manage the specified user.
     */
    private function authorizeAccess(User $user)
    {
        $currentUser = Auth::user();

        // Superadmin can access all users
        if ($currentUser->role == 'superadmin') {
            return true;
        }

        // Other users can only access users they created
        if ($user->pustu_id != $currentUser->id) {
            abort(403, 'Unauthorized action.');
        }

        return true;
    }

    /**
     * Validate if current user has permission to assign the specified role.
     */
    private function validateRoleAccess($currentUserRole, $requestedRole)
    {
        $allowed = false;

        switch ($currentUserRole) {
            case 'superadmin':
                // Superadmin can assign any role
                $allowed = true;
                break;

            case 'puskesmas':
            case 'klinik':
                // Puskesmas and Klinik can create Pustu and healthcare staff
                $allowedRoles = ['pustu', 'dokter', 'perawat', 'operator', 'caregiver', 'farmasi', 'pendaftaran'];
                $allowed = in_array($requestedRole, $allowedRoles);
                break;

            case 'pustu':
                // Pustu can only create healthcare staff
                $allowedRoles = ['dokter', 'perawat', 'operator', 'farmasi', 'caregiver', 'pendaftaran'];
                $allowed = in_array($requestedRole, $allowedRoles);
                break;

            default:
                $allowed = false;
        }

        if (!$allowed) {
            abort(403, 'Anda tidak memiliki izin untuk membuat user dengan role tersebut.');
        }

        return true;
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new UserImport, $request->file('file'));

        return back()->with('success', 'Data pengguna berhasil diimport!');
    }

    /**
     * Show the form for editing current user's profile.
     */
    public function editProfile()
    {
        $user = Auth::user();
        // dd($user);
        return view('users.profile', compact('user'));
    }

    /**
     * Update the current user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'no_wa' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'status_pegawai' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update data
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'no_wa' => $request->no_wa,
            'keterangan' => $request->keterangan,
            'village' => $request->village,
            'district' => $request->district,
            'regency' => $request->regency,
            'status_pegawai' => $request->status_pegawai
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()
            ->back()
            ->with('success', 'Profil berhasil diperbarui!');
    }
}
