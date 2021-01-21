<?php
   
namespace App\Http\Controllers\Api\Auth;

   
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\TermsandConditions;
use Validator;
use App\Http\Resources\TermsandConditions as TermsandConditionsResource;
   
class TermsandConditionsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTermsandConditions()
    {
        //dd(10);
        $termsandconditions = TermsandConditions::all();
        //dd($termsandconditions);
        return $this->sendResponse(TermsandConditionsResource::collection($termsandconditions), 'Terms & Conditions retrieved successfully.');
    }
    
}