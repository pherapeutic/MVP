<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TherapistType;

class TherapistTypeController extends Controller
{
    public function getTherapistTypes(Request $request, TherapistType $therapistType){
        $therapistTypeColl = $therapistType->getAllTherapistTypes();
        $returnArr = array();
        foreach ($therapistTypeColl as $key => $therapistTypeObj) {
            $returnArr[] = $therapistTypeObj->getResponseArr();
        }
        return returnSuccessResponse('Therapist types list', $returnArr);
    }
}
