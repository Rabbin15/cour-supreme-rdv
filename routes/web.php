<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\RendezVousConfirme;
use App\Models\RendezVous;

Route::get('/', function () {
    return view('welcome');
});

// Route de test pour l'envoi d'email
Route::get('/test-mail', function () {
    try {
        // Récupérer le premier rendez-vous en attente
        $rdv = RendezVous::with(['structure', 'creneau.agent'])->first();
        
        if (!$rdv) {
            return "❌ Aucun rendez-vous trouvé en base. Crée d'abord un rendez-vous.";
        }
        
        // Envoyer l'email avec le vrai rendez-vous
        Mail::to($rdv->email_citoyen)->send(new RendezVousConfirme($rdv, 'accepte'));
        
        return "✅ Email envoyé avec succès pour le rendez-vous #{$rdv->id_rdv} ! Vérifie ta boîte Mailtrap.";
    } catch (\Exception $e) {
        return "❌ Erreur : " . $e->getMessage();
    }
});