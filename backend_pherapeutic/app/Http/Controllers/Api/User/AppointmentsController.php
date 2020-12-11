<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\FeedbackNotes;
use App\Models\Appointments;
use Validator;
use Carbon\Carbon;
class AppointmentsController extends Controller
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

    public function getRating(Request $request, Rating $rating){
        $respone = $rating->getAllRatings();
        if(!empty($respone)){
            return returnSuccessResponse('Rating list',$respone);
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

    public function getTherapistAppointment(Request $request, Appointments $appointments){

        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        $appointments = $appointments->getAllTherapistAppointments($userObj->id);

        foreach ($appointments as $appointment) {
            $rating = Rating::where('appointment_id', '=', $appointment->id)->first();
            if($rating){
                $appointment['rating'] = $rating->rating;
                $appointment['comment'] = $rating->comment;
            }else{
                $appointment['rating'] ="0";
                $appointment['comment'] ="";
            }
        }

        if(!empty($appointments)){
            return returnSuccessResponse('Appointments list',$appointments);
        }else{
            return returnNotFoundResponse('Not found');   
        }
    }

    public function getClientAppointment(Request $request, Appointments $appointments){

        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        $appointments = $appointments->getAllClientAppointments($userObj->id);

        foreach ($appointments as $appointment) {
            $rating = Rating::where('appointment_id', '=', $appointment->id)->first();
            if($rating){
                $appointment['rating'] = $rating->rating;
                $appointment['comment'] = $rating->comment;                
            }else{
                $appointment['rating'] ="0";
                $appointment['comment'] ="";
            }
        }

        if(!empty($appointments)){
            return returnSuccessResponse('Appointments list',$appointments);
        }else{
            return returnNotFoundResponse('Not found');   
        }
    }
    
    public function postAppointment(Request $request, Appointments $appointments){

        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        $rules = [
            //'user_id' => 'required',
            'therapist_id' => 'required',
            'is_trail' => 'required',
        ];
        $userObj = $this->request->user();
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }else{
            $inputArr['user_id'] = $userObj->id;
            $respone = $appointments->saveNewAppointment($inputArr);
            if($respone){
                return returnSuccessResponse($respone,'Thanks for Appointment. We update to you soon !');
            }else{
                return returnNotFoundResponse('Something wrong');
           }
        }
    }

    public function postRating(Request $request, Rating $rating){
        $rules = [
            'appointment_id' => 'required',
            'rating' => 'required',
        ];
        $userObj = $this->request->user();
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }else{
            //$respone = $rating->saveNewRating($inputArr);
            $userrating = $rating->getRatingByClientId($inputArr['appointment_id']);
            if($userrating){
                $respone = $rating->updateUserRating($inputArr['appointment_id'],$inputArr);
            }else{
                $respone = $rating->saveNewRating($inputArr);
            }
            
            if($respone){
                return returnSuccessResponse('Thanks for rating !');
            }else{
                return returnNotFoundResponse('Something wrong');
           }
        }    
    }

    public function postFeedback(Request $request, FeedbackNotes $feedback){
        $rules = [
            'appointment_id' => 'required',
            'feedback_note' => 'required',
            'feedback_by' => 'required',
        ];
        $userObj = $this->request->user();
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }else{
            //$respone = $feedback->saveNewFeedback($inputArr);

            $userrating = $feedback->getRatingByAppointmentId($inputArr['appointment_id']);
            if($userrating){
                $respone = $feedback->updateUserFeedback($inputArr['appointment_id'],$inputArr);
            }else{
                $respone = $feedback->saveNewFeedback($inputArr);
            }

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


    public function getTherapistAppointmentRequest(Request $request, Appointments $appointments){

        $rules = [
            'status' => 'required',
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }
        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }

        $appointmentRequestArr = $appointments->getTherapistAppointmentRequests($userObj->id, $inputArr['status']);
        $returnArr = array();
        foreach ($appointmentRequestArr as $key => $appointmentRequestObj) {
            $data = $appointmentRequestObj->getResponseArr();
            array_push($returnArr, $data);
        }

        if(!empty($returnArr)){
            return returnSuccessResponse('Get appointment request',$returnArr);
        }else{
            return returnNotFoundResponse('Not found');
        }
    }

    public function changeAppointmentStatus(Request $request, Appointments $appointments){

        $rules = [
            'appointment_id' => 'required',
            'status' => 'required',
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }
        $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }
        $appointmentObj = $appointments->getAppointmentById($inputArr['appointment_id']);
        if(!$appointmentObj){
            return returnNotFoundResponse('Appointment not found');            
        }
        $updateData = [];
        if($inputArr['status'] == '2'){
            $updateData['ended_at'] = Carbon::now();
        }
        $updateData['status'] = $inputArr['status'];

        $hasUpdate = $appointments->updateAppointment($appointmentObj->id, $updateData);

        if(!empty($hasUpdate)){
            return returnSuccessResponse('Appointment status update',$hasUpdate);
        }else{
            return returnNotFoundResponse('Not found');
        }
    }

}
