<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TherapistType;

class TherapistTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(TherapistType $therapistType)
    {
        $therapistTypes = ['Psychiatrist', 'Psychologist', 'Psychotherapist', 'Cognitive Behaviour Therapist', 'Councillor', 'Life Coach'];
        foreach($therapistTypes as $key => $therapistTypeTitle){
            $therapistType->saveNewTherapistType(['title' => $therapistTypeTitle]);
        }
        
    }
}
