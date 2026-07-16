<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RendezVous;
use App\Models\Creneau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RendezVousController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_creneau' => 'required|exists:creneaux,id_creneau',
            'nom_complet' => 'required|string|max:100',
            'email_citoyen' => 'required|email|max:100',
            'telephone' => 'nullable|string|max:20',
            'motif' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $creneau = Creneau::find($request->id_creneau);
        if (!$creneau || !$creneau->est_disponible) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ce créneau n\'est pas disponible'
            ], 404);
        }

        $existe = RendezVous::where('id_creneau', $request->id_creneau)
            ->whereIn('statut', ['en_attente', 'accepte'])
            ->exists();

        if ($existe) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ce créneau est déjà réservé'
            ], 409);
        }

        $token = Str::random(64);

        $rdv = RendezVous::create([
            'id_creneau' => $request->id_creneau,
            'id_structure' => $creneau->id_structure,
            'nom_complet' => $request->nom_complet,
            'email_citoyen' => $request->email_citoyen,
            'telephone' => $request->telephone,
            'motif' => $request->motif,
            'statut' => 'en_attente',
            'token_annulation' => $token
        ]);

        $rdv->load(['creneau.agent', 'structure']);

        return response()->json([
            'status' => 'success',
            'message' => 'Rendez-vous enregistré avec succès',
            'data' => $rdv
        ], 201);
    }

    public function annuler($token)
    {
        $rdv = RendezVous::where('token_annulation', $token)
            ->whereIn('statut', ['en_attente', 'accepte'])
            ->first();

        if (!$rdv) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token invalide ou rendez-vous déjà traité'
            ], 404);
        }

        $rdv->update(['statut' => 'annule']);

        return response()->json([
            'status' => 'success',
            'message' => 'Votre rendez-vous a été annulé avec succès'
        ]);
    }
}