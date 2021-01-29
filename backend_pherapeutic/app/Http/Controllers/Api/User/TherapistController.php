<?php

namespace App\Http\Controllers\Api\User;

use Auth;
use Validator;
use App\Models\User;
use App\Models\UserAnswers;
use App\Models\Appointments;
use Illuminate\Http\Request;
use App\Models\TherapistType;
use App\Models\TherapistProfile;
use App\Models\UserTherapistType;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class TherapistController extends Controller
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
    public function searchTherapist(Request $request, UserAnswers $userAnswers, TherapistType $therapistType)
    {
       $input = $request->all();
       $userId = Auth::id();

       //Get user point and calculate
       $userAnswer = $userAnswers->getUserAnswerByUserId($userId);
       $userPoints = 0;
       if($userAnswer){
           foreach ($userAnswer as $value) {
               $userPoints += $value->points;
           }
       }
       $userTherapistTypeColl = $therapistType->searchTherapist($userPoints,$input['latitude'],$input['longitude']);

       $returnResponse = array();
       foreach ($userTherapistTypeColl as $key => $userTherapistTypeObj) {
          if(in_array($userTherapistTypeObj->appointmentStatus, ['0', '1'])){
              continue;
          }

           $data = 
               ['user_id' => $userTherapistTypeObj->user_id,
                'first_name' => $userTherapistTypeObj->first_name,
                'last_name' => $userTherapistTypeObj->last_name,
                'email' => $userTherapistTypeObj->email,
                'image' => $userTherapistTypeObj->image,
                'address' => $userTherapistTypeObj->address,
                'latitude' => $userTherapistTypeObj->latitude,
                'longitude' => $userTherapistTypeObj->longitude,
                'experience' => $userTherapistTypeObj->experience,
                'qualification' => $userTherapistTypeObj->qualification
               ];
            array_push($returnResponse, $data);
       }

        return $this->successResponse($returnResponse, 'Therapist List.');
    }

    public function searchTherapistList(Request $request, UserAnswers $userAnswers, TherapistType $therapistType)
    {
       $input = $request->all();
       $userId = Auth::id();

       //Get user point and calculate
       $userAnswer = $userAnswers->getUserAnswerByUserId($userId);
       $userPoints = 0;
       if($userAnswer){
           foreach ($userAnswer as $value) {
               $userPoints += $value->points;
           }
       }
       $userTherapistTypeColl = $therapistType->searchTherapistList($userPoints,$input['latitude'],$input['longitude']);

       $returnResponse = array();
       foreach ($userTherapistTypeColl as $key => $userTherapistTypeObj) {
          if(in_array($userTherapistTypeObj->appointmentStatus, ['0', '1'])){
              continue;
          }

           $data = 
               ['user_id' => $userTherapistTypeObj->user_id,
                'first_name' => $userTherapistTypeObj->first_name,
                'last_name' => $userTherapistTypeObj->last_name,
                'email' => $userTherapistTypeObj->email,
                'image' => $userTherapistTypeObj->image,
                'address' => $userTherapistTypeObj->address,
                'latitude' => $userTherapistTypeObj->latitude,
                'longitude' => $userTherapistTypeObj->longitude,
                'experience' => $userTherapistTypeObj->experience,
                'qualification' => $userTherapistTypeObj->qualification,
                'Languages' => $userTherapistTypeObj->title,
                'Specialism' => $userTherapistTypeObj->name,
                'rating' => $userTherapistTypeObj->rating,
                'comment' => $userTherapistTypeObj->comment,
                'amount' => $userTherapistTypeObj->amount,
                'transfer_amount' => $userTherapistTypeObj->transfer_amount,
                'refund_amount' => $userTherapistTypeObj->refund_amount,
                'consultations_count' =>$userTherapistTypeObj->getCallLogCount($userTherapistTypeObj->user_id)
                
               ];
            array_push($returnResponse, $data);
       }
       if(isset($request->default) && $request->default == 2){
          $returnResponse = array();
          $userTherapistTypeObj = $therapistType->getOnlineTherapist();
           $data = 
               ['user_id' => $userTherapistTypeObj->user_id,
                'first_name' => $userTherapistTypeObj->first_name,
                'last_name' => $userTherapistTypeObj->last_name,
                'email' => $userTherapistTypeObj->email,
                'image' => $userTherapistTypeObj->image,
                'address' => $userTherapistTypeObj->address,
                'latitude' => $userTherapistTypeObj->latitude,
                'longitude' => $userTherapistTypeObj->longitude,
                'experience' => $userTherapistTypeObj->experience,
                'qualification' => $userTherapistTypeObj->qualification,
                'Languages' => $userTherapistTypeObj->title,
                'Specialism' => $userTherapistTypeObj->name,
                'rating' => $userTherapistTypeObj->rating,
                'comment' => $userTherapistTypeObj->comment,
                'amount' => $userTherapistTypeObj->amount,
                'transfer_amount' => $userTherapistTypeObj->transfer_amount,
                'refund_amount' => $userTherapistTypeObj->refund_amount,
                'consultations_count' =>$userTherapistTypeObj->getCallLogCount($userTherapistTypeObj->user_id)
                
               ];
          array_push($returnResponse, $data);

       
     }


        return $this->successResponse($returnResponse, 'Therapist List.');
    }

    public function showAssignedTherapist(Request $request){

        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        $rules = [
            'therapist_id' => 'required',
        ];
        $userObj = $this->request->user();
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }else{
            $appoinmentObj = Appointments::where('therapist_id',$inputArr['therapist_id'])
                                            ->where('user_id', $userObj->id)->first();
            if(!$appoinmentObj){
                return returnNotFoundResponse('Something wrong');
            }
            $returnArr = $appoinmentObj->therapist->getResponseArr();

            return $this->successResponse($returnArr, ' Assigned therapist detail.');            
        }        
    }
    public function store(Request $request){
		$validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required',
                'specialisms' => 'required',
                'qualification' => 'required',
                'experience' => 'required',
                    ]);
        	if ($validator->fails()) {
                return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first()
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
       		 }
            $user= User::create([
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'		=> $request->email,
                'role'  => '1'
                ]);
            $therapist_profiles= TherapistProfile::create([
                'user_id' => $user->id,
                'experience' => $request->experience,
                'qualification' => $request-> qualification,
            ]);

            $user_therapist_types= UserTherapistType ::create([
                'user_id' => $user->id,
                'therapist_type_id' => $request->specialisms,
            ]);
		
		return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Therapist created Successfully'
            ], JsonResponse::HTTP_OK);
		
}

}
