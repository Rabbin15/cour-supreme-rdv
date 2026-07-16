<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Creneau extends Model
{
    protected $table = 'creneaux';
    protected $primaryKey = 'id_creneau';
    public $timestamps = false;

    protected $fillable = [
        'id_agent',
        'id_structure',
        'jour_semaine',
        'heure_debut',
        'heure_fin',
        'date_specifique',
        'est_disponible'
    ];

    // Relation avec l'agent qui a créé ce créneau
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'id_agent', 'id_agent');
    }

    // Relation avec la structure
    public function structure()
    {
        return $this->belongsTo(Structure::class, 'id_structure', 'id_structure');
    }

    // Relation avec les rendez-vous pris sur ce créneau
    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'id_creneau', 'id_creneau');
    }
}