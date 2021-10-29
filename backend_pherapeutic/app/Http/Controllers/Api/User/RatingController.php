<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\FeedbackNotes;
use App\Models\Appointments;
use App\Models\CallLogs;
use Validator;
class RatingController extends Controller
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

    public function getRating(Request $request, CallLogs $callLogs){


        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

         $addRating = 0;
        $totalRating = 0;

      
        $callLogs = $callLogs->getAllTherapistCallLog($userObj->id);


        $responeArr = array();
        foreach ($callLogs as $callLog) {

        if($callLog->ratings){


             $addRating += $callLog->ratings->rating;

            $totalRating++;
        
            array_push($responeArr, $callLog->ratings);
        }
    }

       $ratingAvg = "";

       if(!empty($addRating))
          $ratingAvg = $addRating/$totalRating;

        if(!empty($responeArr)){
            return returnSuccessResponse('Rating list',$responeArr);
        }else{
            return returnNotFoundResponse('Not found');   
        }
    }

    public function getFeedback(Request $request, FeedbackNotes $feedback){
        $respone = $feedback->getAllFeedbacks();
        if(!empty($respone)){
            return returnSuccessResponse('Feedback list',$respone);
        }else{
            return returnNotFoundResponse('Not found');   
        }
    }

    public function getAppointment(Request $request, Appointments $appointments){
        $respone = $appointments->getAllAppointments();
        if(!empty($respone)){
            return returnSuccessResponse('Feedback list',$respone);
        }else{
            return returnNotFoundResponse('Not found');   
        }
    }
    
    public function clientPostRating(Request $request, Rating $rating){
        $rules = [
            'call_logs_id' => 'required',
            'rating' => 'required',
            'comment' => 'required',
        ];
        $userObj = $this->request->user();
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }

        $ratingExist = $rating->getRatingByCallLogId($inputArr['call_logs_id']);
        if($ratingExist){
            return returnValidationErrorResponse('Call rating is already post');            
        }

        $respone = $rating->saveNewRating($inputArr);
        //$userrating = $rating->getRatingByClientId($userObj->id);
        // $inputArr['client_id'] = $userObj->id;
        // if($userrating){
        //     $respone = $rating->updateUserRating($userObj->id,$inputArr);
        // }else{
        //     $respone = $rating->saveNewRating($inputArr);
        // }
        
        if($respone){
            return returnSuccessResponse('Thanks for rating !');
        }else{
            return returnNotFoundResponse('Something wrong');
        }
    }

    public function therapistPostFeedback(Request $request, FeedbackNotes $feedback){
        $rules = [
            'call_logs_id' => 'required',
            'feedback_note' => 'required',
        ];
        $userObj = $this->request->user();
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }else{
            $inputArr['feedback_by'] = $userObj->id;
            $respone = $feedback->saveNewFeedback($inputArr);

            if($respone){
                return returnSuccessResponse('Thanks for feedback !');
            }else{
                return returnNotFoundResponse('Something wrong');
           }
        }    
    }

    // public function UpdateRating(Request $request, Rating $rating){
    //     $rules = [
    //         //'client_id' => 'required',
    //         'therapist_id' => 'required',
    //         'rating' => 'required',
    //     ];

    //     $userObj = $this->request->user();
    //     $inputArr = $request->all();
    //     $validator = Validator::make($inputArr, $rules);
    //     if ($validator->fails()) {
    //         $validateerror = $validator->errors()->all();
    //         return $this->validationErrorResponse($validateerror[0]);
    //     }else{
    //             $inputArr['client_id'] = $userObj->id;
    //             //echo $userObj->id;die;
    //             $respone = $rating->updateUserRating($userObj->id,$inputArr);
    //             if($respone){
    //                 return returnSuccessResponse('Thanks for rating !');
    //             }else{
    //                 return returnNotFoundResponse('Something wrong');
    //             }
    //     }    
    // }

    public function DeleteRating(Request $request, Rating $rating){

        $userObj = $this->request->user();
        $userrating = $rating->getRatingByClientId($userObj->id);

        if(!$userrating){
            return returnNotFoundResponse('This Rating does not exist');
        }

        $hasDeleted = $userrating->delete();
        if($hasDeleted){
            return returnSuccessResponse('Rating deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later'); 
    }
}
