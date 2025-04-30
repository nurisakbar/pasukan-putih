<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pustu;
use App\Models\District;
use App\Models\Village;
use DB;

class PustuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pustu = Pustu::with(['districts', 'villages'])->latest()->get();

        return view('pustu.index', compact('pustu'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pustu.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pustu' => 'required',
            'village_id' => 'required',
            'district_id' => 'required',
        ]);

        Pustu::create($request->all());

        return redirect()->route('pustu.index')->with('success', 'Created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pustu = Pustu::find($id);
        if ($pustu->village_id) {
            $selectedVillage = DB::table('villages')
                ->join('districts', 'villages.district_id', '=', 'districts.id')
                ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
                ->join('provinces', 'regencies.province_id', '=', 'provinces.id')
                ->select(
                    'villages.id as village_id',
                    'villages.name as village_name',
                    'districts.id as district_id',
                    'districts.name as district_name',
                    'regencies.name as regency_name',
                    'provinces.name as province_name'
                )
                ->where('villages.id', $pustu->village_id)
                ->first();
        } elseif ($pustu->district_id) {
            $selectedVillage = DB::table('districts')
                ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
                ->join('provinces', 'regencies.province_id', '=', 'provinces.id')
                ->select(
                    DB::raw('NULL as village_id'),
                    DB::raw('NULL as village_name'),
                    'districts.id as district_id',
                    'districts.name as district_name',
                    'regencies.name as regency_name',
                    'provinces.name as province_name'
                )
                ->where('districts.id', $pustu->district_id)
                ->first();
        } else {
            $selectedVillage = null;
        }        

        return view('pustu.edit', compact('pustu', 'selectedVillage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_pustu' => 'required',
            'village_id' => 'required',
            'district_id' => 'required',
        ]);

        $pustu = Pustu::find($id);
        $pustu->update($request->all());

        return redirect()->route('pustu.index')->with('success', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pustu = Pustu::find($id);
        $pustu->delete();

        return redirect()->route('pustu.index')->with('success', 'Deleted successfully');
    }
}
