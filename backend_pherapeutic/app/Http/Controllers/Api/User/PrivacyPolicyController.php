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
		$getResponseData=array();
        $privacypolicy = PrivacyPolicy::all();
		if($privacypolicy->count() >0){
			foreach ($privacypolicy as $policy) {
            $getResponseData[] = $policy->getResponseArr();
            
        }
		}
		// print_r($getResponseData);die;
        //dd($privacypolicy);
        // return $this->sendResponse(PrivacyPolicyResource::collection($getResponseData), 'Privacy Policy retrieved successfully.');
		return returnSuccessResponse('Privacy Policy retrieved successfully.',$getResponseData);
    }
    
}