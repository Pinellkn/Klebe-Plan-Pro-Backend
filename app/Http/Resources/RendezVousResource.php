<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Formate un rendez-vous pour le front (Josephine/Shalom) :
 * champs stables, pas de fuite de colonnes internes.
 */
class RendezVousResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'date' => $this->date?->format('Y-m-d'),
            'heure' => $this->heure,
            'lieu' => $this->lieu,
            'statut' => $this->statut,
            'notes' => $this->notes,
            'cree_par' => $this->whenLoaded('creePar', fn () => [
                'id' => $this->creePar->id,
                'nom' => $this->creePar->nom,
            ]),
            'rappels' => [
                'veille_envoye' => $this->rappel_veille_envoye_a !== null,
                'jour_j_envoye' => $this->rappel_jour_j_envoye_a !== null,
                '15min_envoye' => $this->rappel_15min_envoye_a !== null,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
