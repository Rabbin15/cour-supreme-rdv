<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Structure;
use Illuminate\Http\Request;

class StructureController extends Controller
{
    public function index()
    {
        $structures = Structure::all();
        return response()->json([
            'status' => 'success',
            'data' => $structures
        ]);
    }

    public function show($id)
    {
        $structure = Structure::with(['creneaux.agent'])->find($id);
        if (!$structure) {
            return response()->json([
                'status' => 'error',
                'message' => 'Structure non trouvée'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $structure
        ]);
    }
}