<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    protected $table = 'structures';
    protected $primaryKey = 'id_structure';
    public $timestamps = false;

    protected $fillable = [
        'nom_structure',
        'description'
    ];

    public function agents()
    {
        return $this->hasMany(Agent::class, 'id_structure', 'id_structure');
    }

    public function creneaux()
    {
        return $this->hasMany(Creneau::class, 'id_structure', 'id_structure');
    }
}