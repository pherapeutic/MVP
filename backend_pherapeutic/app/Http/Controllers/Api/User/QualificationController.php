<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Qualification;

class QualificationController extends Controller
{
    public function index(Qualification $qualification){
		$data=$qualification->getQualification();
		return returnSuccessResponse('Get all qualification .',$data);
	}
}
