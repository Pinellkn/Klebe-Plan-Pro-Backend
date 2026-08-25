<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entreprise extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'telephone_dg',
        'nom_dg',
        'plan',
        'plan_actif_jusqu_au',
        'quota_mensuel',
        'quota_utilise',
        'quota_packs_supplementaires',
        'quota_reinitialise_le',
        'actif',
    ];

    protected $casts = [
        'plan_actif_jusqu_au' => 'datetime',
        'quota_reinitialise_le' => 'datetime',
        'actif' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    /**
     * Nombre de messages encore disponibles ce mois-ci.
     * (quota inclus dans le plan + packs achetés) - messages déjà utilisés.
     */
    public function quotaRestant(): int
    {
        $total = $this->quota_mensuel + ($this->quota_packs_supplementaires * 100);

        return max(0, $total - $this->quota_utilise);
    }

    public function quotaAtteint(): bool
    {
        return $this->quotaRestant() <= 0;
    }
}
