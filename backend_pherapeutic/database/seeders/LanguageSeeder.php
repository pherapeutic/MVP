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
        $languages = ['Hindi', 'English', 'French'];
        foreach($languages as $key => $languageName){
            $language->saveNewLanguage(['title' => $languageName]);
        }
        
    }
}
