<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StructureController;
use App\Http\Controllers\Api\CreneauController;
use App\Http\Controllers\Api\RendezVousController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AgentController;

// ============================================
// ROUTES PUBLIQUES (sans authentification)
// ============================================
Route::get('/structures', [StructureController::class, 'index']);
Route::get('/structures/{id}', [StructureController::class, 'show']);
Route::get('/structures/{id}/creneaux', [CreneauController::class, 'getByStructure']);

Route::post('/rendez-vous', [RendezVousController::class, 'store']);
Route::get('/rendez-vous/annuler/{token}', [RendezVousController::class, 'annuler']);

Route::post('/login', [AuthController::class, 'login']);

// ============================================
// ROUTES PROTÉGÉES (authentification requise)
// ============================================
Route::middleware('auth:sanctum')->group(function () {
    // Gestion du compte
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Gestion des créneaux de l'agent connecté
    Route::get('/agent/creneaux', [AgentController::class, 'getCreneaux']);

    // Gestion des rendez-vous reçus par l'agent
    Route::get('/agent/rendez-vous', [AgentController::class, 'getRendezVous']);
    Route::put('/agent/rendez-vous/{id}/accepter', [AgentController::class, 'accepterRendezVous']);
    Route::put('/agent/rendez-vous/{id}/refuser', [AgentController::class, 'refuserRendezVous']);
});