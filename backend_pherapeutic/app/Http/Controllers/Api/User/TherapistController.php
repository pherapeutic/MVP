<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserAnswers;
use App\Models\TherapistType;
use App\Models\User;
use App\Models\UserTherapistType;
use App\Models\TherapistProfile;
use App\Models\Appointments;
use Auth;
use Validator;
use Illuminate\Support\Facades\DB;

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

    // public function searchTherapistList(Request $request, UserAnswers $userAnswers, TherapistType $therapistType)
    // {
    //    $input = $request->all();
    //    $userId = Auth::id();

    //    //Get user point and calculate
    //    $userAnswer = $userAnswers->getUserAnswerByUserId($userId);
    //    $userPoints = 0;
    //    if($userAnswer){
    //        foreach ($userAnswer as $value) {
    //            $userPoints += $value->points;
    //        }
    //    }
    //    $userTherapistTypeColl = $therapistType->searchTherapistList($userPoints,$input['latitude'],$input['longitude']);

    //    $returnResponse = array();
    //    foreach ($userTherapistTypeColl as $key => $userTherapistTypeObj) {
    //       if(in_array($userTherapistTypeObj->appointmentStatus, ['0', '1'])){
    //           continue;
    //       }

    //        $data = 
    //            ['user_id' => $userTherapistTypeObj->user_id,
    //             'first_name' => $userTherapistTypeObj->first_name,
    //             'last_name' => $userTherapistTypeObj->last_name,
    //             'email' => $userTherapistTypeObj->email,
    //             'image' => $userTherapistTypeObj->image,
    //             'address' => $userTherapistTypeObj->address,
    //             'latitude' => $userTherapistTypeObj->latitude,
    //             'longitude' => $userTherapistTypeObj->longitude,
    //             'experience' => $userTherapistTypeObj->experience,
    //             'qualification' => $userTherapistTypeObj->qualification,
    //             'Languages' => $userTherapistTypeObj->title,
    //             'Specialism' => $userTherapistTypeObj->name,
    //             'rating' => !empty($$userTherapistTypeObj->rating)?$userTherapistTypeObj->rating:0,
    //             'comment' => $userTherapistTypeObj->comment,
    //             'amount' => $userTherapistTypeObj->amount,
    //             'transfer_amount' => $userTherapistTypeObj->transfer_amount,
    //             'refund_amount' => $userTherapistTypeObj->refund_amount,
    //             'consultations_count' =>$userTherapistTypeObj->getCallLogCount($userTherapistTypeObj->user_id)
                
    //            ];
    //         array_push($returnResponse, $data);
    //    }
    //    if(isset($request->default) && $request->default == 2){
    //       $returnResponse = array();
    //       $userTherapistTypeObj = $therapistType->getOnlineTherapist();
    //        $data = 
    //            ['user_id' => $userTherapistTypeObj->user_id,
    //             'first_name' => $userTherapistTypeObj->first_name,
    //             'last_name' => $userTherapistTypeObj->last_name,
    //             'email' => $userTherapistTypeObj->email,
    //             'image' => $userTherapistTypeObj->image,
    //             'address' => $userTherapistTypeObj->address,
    //             'latitude' => $userTherapistTypeObj->latitude,
    //             'longitude' => $userTherapistTypeObj->longitude,
    //             'experience' => $userTherapistTypeObj->experience,
    //             'qualification' => $userTherapistTypeObj->qualification,
    //             'Languages' => $userTherapistTypeObj->title,
    //             'Specialism' => $userTherapistTypeObj->name,
    //             'rating' => $userTherapistTypeObj->rating,
    //             'comment' => $userTherapistTypeObj->comment,
    //             'amount' => $userTherapistTypeObj->amount,
    //             'transfer_amount' => $userTherapistTypeObj->transfer_amount,
    //             'refund_amount' => $userTherapistTypeObj->refund_amount,
    //             'consultations_count' =>$userTherapistTypeObj->getCallLogCount($userTherapistTypeObj->user_id)
                
    //            ];
    //       array_push($returnResponse, $data);

       
    //  }


    //     return $this->successResponse($returnResponse, 'Therapist List.');
    // }

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

    public function clientList(Request $request){

      $therapistProfile = auth()->user();
	  
	  // print_r($therapistProfile);die('a');

      if(empty($therapistProfile->latitude) || empty($therapistProfile->longitude))
          return returnNotFoundResponse('latitude and longitude not found.');

        $distance = 10;

      $clients = User::where(['role'=>User::CLIENT_ROLE])
->select('*', DB::raw("( 6371 * acos( cos( radians($therapistProfile->latitude) ) * cos( radians( latitude ) )* cos( radians( longitude ) - radians($therapistProfile->longitude)) + sin( radians($therapistProfile->latitude) ) *sin( radians( latitude ) ) )) AS distance"))->orderBy('distance','ASC')
                     ->having('distance', '<', $distance)->get();
                    // $user_list=  $user_list->having('distance', '<', $distance)->inRandomOrder()->limit(1)->get();

      $response = [];

      foreach ($clients as $key => $client) {

        $data['title'] =mb_convert_encoding( $client->first_name. ' '.$client->last_name, 'UTF-8', 'UTF-8');
        $data['coordinates']['latitude'] = (float) $client->latitude;
        $data['coordinates']['longitude'] = (float) $client->longitude;

        array_push($response, $data);

        }


        return $this->successResponse(' Client List sent successfully.',$response);            

    }

    public function searchTherapistList(Request $request,UserAnswers $userAnswers, TherapistType $therapistType){

       $input = $request->all();
       $userId = Auth::id();

       $latitude = $input['latitude'];
       $longitude = $input['longitude'];

       //Get user point and calculate
       $userAnswer = $userAnswers->getUserAnswerByUserId($userId);
       $userPoints = 0;
       if($userAnswer){
           foreach ($userAnswer as $value) {
               $userPoints += $value->points;
           }
       }

       //$userPoints = 10000;

       // print_r($userPoints);
       // die();


       $therapistType = TherapistType::select('id')->where('min_point','<=',$userPoints)->where('point','>=',$userPoints);

        $userTherapistType = UserTherapistType::select('user_id')->distinct()->whereIn('therapist_type_id',$therapistType);

       $otherTherapistType = TherapistType::select('id');

        $otherTherapistType = UserTherapistType::select('user_id')->distinct()->whereIn('therapist_type_id',$otherTherapistType);

        $otherTherapists = User::whereIn('id',$otherTherapistType)->whereNotIn('id',$userTherapistType)->where('online_status', '1')->where('email_verified_at','!=','null')->get();

        $pointTherapists = User::whereIn('id',$userTherapistType)->where('online_status', '1')->where('email_verified_at','!=','null')->get();

       $therapists = $pointTherapists->merge($otherTherapists);

       if(!count($pointTherapists))
        $therapists = [];




       
       if(isset($request->default) && $request->default == 2){

        $latitude = '';
        $longitude = '';

        $therapistType = TherapistType::select('id');

         $userTherapistType = UserTherapistType::select('user_id')->distinct()->whereIn('therapist_type_id',$therapistType);

         $therapists = User::whereIn('id',$userTherapistType)->where('online_status', '1')->where('email_verified_at','!=','null')->get();

       }

      

       //$therapists = User::join('therapist_profiles', 'therapist_profiles.user_id', '=', 'users.id');



      // if(!empty($latitude) && !empty($longitude)){

      //   $distance = 10;

      //   $therapists = $therapists->select('*', DB::raw("( 6371 * acos( cos( radians($latitude) ) * cos( radians( therapist_profiles.latitude ) )* cos( radians( therapist_profiles.longitude ) - radians($longitude)) + sin( radians($latitude) ) *sin( radians( therapist_profiles.latitude ) ) )) AS distance"))->having('distance', '<', $distance); 

      // }


       

      // $therapists = $therapists->whereIn('users.id',$userTherapistType)->where('online_status', '1')->where('email_verified_at','!=','null')->get();

       $returnResponse = [];

       foreach ($therapists as $key => $therapist) {

        //$therapist->id = $therapist->user_id;

        $therapistProfile = $therapist->therapistProfile;


          $data = 
               ['user_id' => $therapist->id,
                'first_name' => $therapist->first_name,
                'last_name' => $therapist->last_name,
                'email' => $therapist->email,
                'image' => $therapist->image,
                'address' => !empty($therapistProfile)?$therapistProfile->address:"",
                'latitude' => !empty($therapistProfile)?$therapistProfile->latitude:"",
                'longitude' => !empty($therapistProfile)?$therapistProfile->longitude:"",
                'experience' => !empty($therapistProfile)?"$therapistProfile->experience":"",
                'qualification' => $therapist->getQualification(),
                'Languages' => $therapist->getLanguage(),
                'Specialism' =>  $therapist->getSpecialism(),
                'rating' =>  $therapist->getAverageRating(),
                //'comment' => $userTherapistTypeObj->comment,
                'amount' => 50,
                //'transfer_amount' => $userTherapistTypeObj->transfer_amount,
                //'refund_amount' => $userTherapistTypeObj->refund_amount,
                'consultations_count' =>$therapist->getConsultationsCount()
                
               ];
            array_push($returnResponse, $data);
       }

      return $this->successResponse($returnResponse, 'Therapist List.');
      

    }
	public function bonoWorkStatus(Request $request){
		 $userObj = $this->request->user();
        if (!$userObj) {
            return $this->notAuthorizedResponse('User is not authorized');
        }
		$inputArr = $request->all();
		  $data=array();
			$data['is_pro_bono_work']=($inputArr['bono_work'])?'1':'0';	
            User::where('id',$userObj->id)->update($data);			
		  return $this->successResponse([], 'Bono work status has been updated successfully');
		
	}
}
