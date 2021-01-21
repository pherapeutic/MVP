<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Language $language)
    {
        $languages = ['Hindi', 'English', 'French','Afrikaans','Albanian','Arabic','Bengali','Bosnian','Bulgarian','Chinese','Danish','Dutch','Filipino','Finnish','French','German','Greek','Gujarati',
        'Hebrew','Hungarian','Igbo','Irish','Italian','Japanese','Korean','Kurdish','Lithuanian','Maori',
        'Norwegian','Panjabi','Polish','Portuguese','Romanian','Serbian','Somali','Spanish','Sudanese','Swahili',
    'Swedish','Tamil','Thai','Turkish','Urdu','Vietnamese','Welsh','Yoruba','None of these'];
        foreach($languages as $key => $languageName){
            $language->saveNewLanguage(['title' => $languageName]);
        }
        
    }
}
