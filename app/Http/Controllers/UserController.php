<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserImport;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $query = User::query();

        // Filter berdasarkan role
        if ($currentUser->role !== 'superadmin') {
            $query->where('parent_id', $currentUser->id);
        }

        // Search berdasarkan nama atau email
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan rentang tanggal
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $users = $query->orderBy('created_at', 'asc')->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $currentUser = Auth::user();
        $parents = collect();
        
        // If user is superadmin, get potential parents for dropdown
        if ($currentUser->role == 'superadmin') {
            // Get all facilities that can be parents
            $parents = User::whereIn('role', ['puskesmas', 'pustu', 'klinik'])
                           ->get();
        }
        
        return view('users.create', compact('parents'));
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
        
        // Additional validation for parent_id when needed
        if ($currentUser->role == 'superadmin' && in_array($request->role, ['pustu', 'dokter', 'perawat', 'farmasi', 'pendaftaran'])) {
            $rules['parent_id'] = ['required', 'exists:users,id'];
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()
                ->route('users.create')
                ->withErrors($validator)
                ->withInput();
        }
        
        // Determine parent_id
        $parentId = null;
        
        // If superadmin and parent_id provided, use it
        if ($currentUser->role == 'superadmin' && $request->has('parent_id') && !empty($request->parent_id)) {
            $parentId = $request->parent_id;
        } 
        // If not superadmin, use current user as parent
        elseif ($currentUser->role != 'superadmin') {
            $parentId = $currentUser->id;
        }
        
        // Role access validation
        $this->validateRoleAccess($currentUser->role, $request->role);
        
        // Create the user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'parent_id' => $parentId,
            'no_wa' => $request->no_wa,
            'keterangan' => $request->keterangan
        ]);
        
        return redirect()
            ->route('users.index')
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
        $this->authorizeAccess($user);
        
        $currentUser = Auth::user();
        $parents = collect();
        
        // If user is superadmin, get potential parents for dropdown
        if ($currentUser->role == 'superadmin') {
            $parents = User::whereIn('role', ['puskesmas', 'pustu', 'klinik'])
                           ->get();
        }
        
        return view('users.edit', compact('user', 'parents'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeAccess($user);
        $currentUser = Auth::user();
        
        // Validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string'],
            'no_wa' => ['string', 'max:255'],
            'keterangan' => ['string', 'max:255', 'nullable'],
        ];
        
        // Password is optional during update
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }
        
        // Additional validation for parent_id when needed
        if ($currentUser->role == 'superadmin' && in_array($request->role, ['pustu', 'dokter', 'perawat', 'farmasi', 'pendaftaran'])) {
            $rules['parent_id'] = ['required', 'exists:users,id'];
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return redirect()
                ->route('users.edit', $user)
                ->withErrors($validator)
                ->withInput();
        }
        
        // Determine parent_id
        $parentId = $user->parent_id;
        
        // If superadmin and parent_id provided, use it
        if ($currentUser->role == 'superadmin' && $request->has('parent_id') && !empty($request->parent_id)) {
            $parentId = $request->parent_id;
        }
        
        // Role access validation
        $this->validateRoleAccess($currentUser->role, $request->role);
        
        // Update user data
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'parent_id' => $parentId,
            'no_wa' => $request->no_wa,
            'keterangan' => $request->keterangan
        ];
        
        // Only update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        
        $user->update($userData);
        
        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->authorizeAccess($user);
        
        // Check if user has child users
        $childUsers = User::where('parent_id', $user->id)->count();
        
        if ($childUsers > 0) {
            return redirect()
                ->route('users.index')
                ->with('error', 'User tidak dapat dihapus karena masih memiliki user-user di bawahnya!');
        }
        
        $user->delete();
        
        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus!');
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
        if ($user->parent_id != $currentUser->id) {
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
                $allowedRoles = ['pustu', 'dokter', 'perawat', 'caregiver', 'farmasi', 'pendaftaran'];
                $allowed = in_array($requestedRole, $allowedRoles);
                break;
                
            case 'pustu':
                // Pustu can only create healthcare staff
                $allowedRoles = ['dokter', 'perawat', 'farmasi', 'caregiver', 'pendaftaran'];
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
}