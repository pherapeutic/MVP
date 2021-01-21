<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserAnswers;
use App\Models\TherapistType;
use App\Models\Appointments;
use Auth;
use Validator;

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
}
