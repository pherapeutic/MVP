<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;

class LanguageController extends Controller
{
   /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function getLanguages(Request $request, Language $language){
        $languageColl = $language->getAllLanguages();
        $returnArr = array();
        foreach ($languageColl as $key => $languageObj) {
            $returnArr[] = $languageObj->getResponseArr();
        }
        return returnSuccessResponse('Language list', $returnArr);
    }

}
