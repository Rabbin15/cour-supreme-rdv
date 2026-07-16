<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    protected $table = 'rendez_vous';
    protected $primaryKey = 'id_rdv';
    public $timestamps = false;

    protected $fillable = [
        'id_creneau',
        'id_structure',
        'nom_complet',
        'email_citoyen',
        'telephone',
        'motif',
        'statut',
        'token_annulation',
        'date_demande',
        'date_reponse',
        'motif_refus'
    ];

    public function creneau()
    {
        return $this->belongsTo(Creneau::class, 'id_creneau', 'id_creneau');
    }

    public function structure()
    {
        return $this->belongsTo(Structure::class, 'id_structure', 'id_structure');
    }
}