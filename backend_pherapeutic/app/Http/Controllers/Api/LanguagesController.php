<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Faqs;
use App\Models\Languages;
class LanguagesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

   /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function getLanguages(Request $request, Languages $languages){
        $languages = $languages->getAllLanguages();
        if(!empty($languages)){
            return returnSuccessResponse('Languages list',$languages);
        }else{
            return returnNotFoundResponse('Not found');   
        }
    }

}
