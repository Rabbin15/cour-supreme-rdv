<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Creneau;
use App\Models\Structure;

class CreneauxSeeder extends Seeder
{
    public function run()
    {
        // Définition des créneaux fixes (Lundi au Vendredi)
        $horaires = [
            'Lundi' => [
                ['10:00', '12:00'],
                ['15:00', '17:00']
            ],
            'Mardi' => [
                ['08:00', '10:00'],
                ['14:00', '16:00']
            ],
            'Mercredi' => [
                ['08:00', '10:00'],
                ['15:00', '17:00']
            ],
            'Jeudi' => [
                ['08:00', '10:00'],
                ['15:00', '17:00']
            ],
            'Vendredi' => [
                ['08:00', '09:00'],
                ['14:00', '16:00']
            ]
        ];

        // Récupérer toutes les structures
        $structures = Structure::all();

        foreach ($structures as $structure) {
            // Récupérer un agent de cette structure (le premier)
            $agent = $structure->agents()->first();
            if (!$agent) continue;

            foreach ($horaires as $jour => $creneaux) {
                foreach ($creneaux as $horaire) {
                    Creneau::create([
                        'id_agent' => $agent->id_agent,
                        'id_structure' => $structure->id_structure,
                        'jour_semaine' => $jour,
                        'heure_debut' => $horaire[0] . ':00',
                        'heure_fin' => $horaire[1] . ':00',
                        'date_specifique' => null,
                        'est_disponible' => true
                    ]);
                }
            }
        }

        echo "✅ Créneaux générés pour toutes les structures !\n";
    }
}