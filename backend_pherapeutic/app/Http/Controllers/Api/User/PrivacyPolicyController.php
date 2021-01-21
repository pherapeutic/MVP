<?php
   
namespace App\Http\Controllers\Api\User;
   
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\PrivacyPolicy;
use Validator;
use App\Http\Resources\PrivacyPolicy as PrivacyPolicyResource;
   
class PrivacyPolicyController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPrivacyPolicy()
    {
        //dd(10);
        $privacypolicy = PrivacyPolicy::all();
        //dd($privacypolicy);
        return $this->sendResponse(PrivacyPolicyResource::collection($privacypolicy), 'Privacy Policy retrieved successfully.');
    }
    
}