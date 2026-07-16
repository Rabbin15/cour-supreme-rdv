<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Creneau;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\RendezVousConfirme;

class AgentController extends Controller
{
    // Récupérer les créneaux de l'agent connecté
    public function getCreneaux(Request $request)
    {
        $agent = $request->user();
        $creneaux = Creneau::where('id_agent', $agent->id_agent)
            ->orderByRaw("FIELD(jour_semaine, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi')")
            ->orderBy('heure_debut')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $creneaux
        ]);
    }

    // Récupérer les rendez-vous reçus par l'agent
    public function getRendezVous(Request $request)
    {
        $agent = $request->user();
        $rdvs = RendezVous::with(['creneau', 'structure'])
            ->where('id_structure', $agent->id_structure)
            ->orderBy('date_demande', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $rdvs
        ]);
    }

    // Accepter un rendez-vous
    public function accepterRendezVous(Request $request, $id)
    {
        $agent = $request->user();
        $rdv = RendezVous::where('id_rdv', $id)
            ->where('id_structure', $agent->id_structure)
            ->where('statut', 'en_attente')
            ->first();

        if (!$rdv) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rendez-vous non trouvé ou déjà traité'
            ], 404);
        }

        $rdv->update([
            'statut' => 'accepte',
            'date_reponse' => now()
        ]);

        // Envoyer un email de confirmation
        try {
            Mail::to($rdv->email_citoyen)->send(new RendezVousConfirme($rdv, 'accepte'));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email acceptation: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Rendez-vous accepté avec succès. Un email a été envoyé.',
            'data' => $rdv
        ]);
    }

    // Refuser un rendez-vous
    public function refuserRendezVous(Request $request, $id)
    {
        $agent = $request->user();
        $rdv = RendezVous::where('id_rdv', $id)
            ->where('id_structure', $agent->id_structure)
            ->where('statut', 'en_attente')
            ->first();

        if (!$rdv) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rendez-vous non trouvé ou déjà traité'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'motif_refus' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $rdv->update([
            'statut' => 'refuse',
            'date_reponse' => now(),
            'motif_refus' => $request->motif_refus
        ]);

        // Envoyer un email de refus
        try {
            Mail::to($rdv->email_citoyen)->send(new RendezVousConfirme($rdv, 'refuse'));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email refus: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Rendez-vous refusé. Un email a été envoyé.',
            'data' => $rdv
        ]);
    }
}