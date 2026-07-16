<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Creneau;
use Illuminate\Http\Request;

class CreneauController extends Controller
{
    public function getByStructure($id_structure)
    {
        $creneaux = Creneau::with('agent')
            ->where('id_structure', $id_structure)
            ->where('est_disponible', 1)
            ->orderByRaw("FIELD(jour_semaine, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi')")
            ->orderBy('heure_debut')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $creneaux
        ]);
    }
}