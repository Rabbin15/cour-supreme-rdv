<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Agent extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'agents';
    protected $primaryKey = 'id_agent';
    public $timestamps = false;

    protected $fillable = [
        'id_structure',
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'role',
        'est_actif'
    ];

    protected $hidden = [
        'password'
    ];

    public function structure()
    {
        return $this->belongsTo(Structure::class, 'id_structure', 'id_structure');
    }

    public function creneaux()
    {
        return $this->hasMany(Creneau::class, 'id_agent', 'id_agent');
    }
}