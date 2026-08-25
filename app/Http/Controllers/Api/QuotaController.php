<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint de lecture du quota — consommé par l'écran "quota" de Keira (front).
 * L'écriture du quota_utilise se fait côté Bilal (à chaque envoi WhatsApp),
 * pas ici : Pinel expose seulement la structure BDD + la lecture.
 */
class QuotaController extends Controller
{
    /**
     * GET /api/quota
     */
    public function show(Request $request): JsonResponse
    {
        $entreprise = $request->user()->entreprise;

        return response()->json([
            'data' => [
                'plan' => $entreprise->plan,
                'quota_mensuel' => $entreprise->quota_mensuel,
                'quota_utilise' => $entreprise->quota_utilise,
                'quota_packs_supplementaires' => $entreprise->quota_packs_supplementaires,
                'quota_restant' => $entreprise->quotaRestant(),
                'quota_atteint' => $entreprise->quotaAtteint(),
                'reinitialise_le' => $entreprise->quota_reinitialise_le?->toIso8601String(),
            ],
        ]);
    }
}
