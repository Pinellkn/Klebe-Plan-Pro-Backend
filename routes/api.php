<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuotaController;
use App\Http\Controllers\Api\RendezVousController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes API — Pinel (RDV/équipe/quota) + Bilal (authentification)
|--------------------------------------------------------------------------
| À COLLER dans le routes/api.php du projet Laravel principal.
*/

// --- Authentification (tâche Bilal, publique) ---
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // --- Session (tâche Bilal) ---
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);


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
