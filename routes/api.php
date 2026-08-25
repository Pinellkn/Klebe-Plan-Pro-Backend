<?php

use App\Http\Controllers\Api\QuotaController;
use App\Http\Controllers\Api\RendezVousController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes API — tâche "Pinel — Données & API RDV"
|--------------------------------------------------------------------------
| À COLLER dans le routes/api.php du projet Laravel principal
| (ne pas écraser les routes déjà existantes de Bilal pour l'auth WhatsApp).
|
| L'auth (Sanctum) est branchée par Bilal ; ici on suppose juste que
| le middleware 'auth:sanctum' est disponible.
*/

Route::middleware('auth:sanctum')->group(function () {

    // --- Rendez-vous (CRUD complet) ---
    Route::apiResource('rendez-vous', RendezVousController::class);

    // --- Équipe / permissions ---
    Route::get('equipe', [TeamController::class, 'index']);
    Route::post('equipe', [TeamController::class, 'store']);
    Route::patch('equipe/{membre}/desactiver', [TeamController::class, 'desactiver']);
    Route::patch('equipe/{membre}/reactiver', [TeamController::class, 'reactiver']);
    Route::delete('equipe/{membre}', [TeamController::class, 'destroy']);

    // --- Quota (lecture) ---
    Route::get('quota', [QuotaController::class, 'show']);
});
