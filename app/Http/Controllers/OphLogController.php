<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OphLog;

class OphLogController extends Controller
{
    public function index()
    {
        $logs = OphLog::all();
        $logs = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'data' => unserialize($log->data), 
                'created_at' => $log->created_at,
            ];
        });

        return response()->json([
            'message' => 'Data retrieved successfully',
            'logs' => $logs
        ]);
    }

    public function store(Request $request)
    {
        $serializedData = serialize($request->all());

        $log = OphLog::create([
            'data' => $serializedData,
        ]);

        return response()->json([
            'message' => 'Log saved successfully',
            'log' => $log
        ]);
    }
}
