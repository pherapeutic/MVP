<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;
use App\Models\CallLogs;
use App\Models\PaymentDetails;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    const CLIENT_ROLE = '0';
    const THERAPIST_ROLE = '1';
    const ADMIN_ROLE = '2';
    const ORDER_CANCELED = '3';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userLanguages()
    {
        return $this->hasMany('App\Models\UserLanguage', 'user_id');
    }
    
    public function therapistProfile()
    {
        return $this->hasOne('App\Models\TherapistProfile', 'user_id');
    }

    public function userTherapistTypes()
    {
        return $this->hasMany('App\Models\UserTherapistType', 'user_id');
    }
     /**
     * Use mutator to convert password into hash while creating new club
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getFullNameAttribute($value)
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getTherapistSpecialisation(){

        $specialisms = $this->userTherapistTypes;
        $therapistType = [];
        if($specialisms->count()){
        foreach ($specialisms as $key => $specialism) {

            $therapistType[$key] = $specialism->therapistType->title;
        }
        return implode(',', $therapistType);
       }
       return "N/A";
    }

    public function getLanguagesString()
    {
        $userLangauages = $this->userLanguages;
        $languageArr = array();
        $languageString = '';
        foreach ($userLangauages as $key => $userLangauage) {
            if(!$userLangauage->language)
                continue;
            
            if(count($userLangauages) <= ++$key){
                $languageString .= optional($userLangauage->language)->title;
            } else {
                $languageString .= optional($userLangauage->language)->title.", ";
            }
        }
        return $languageString;
    }

    /**
     * Created By Devendra KUmar
     * Created At 06-11-2020
     * @var array of user input details
     * @return object of user
     * This function use to save new uer's detail in Database
     */
    public function saveNewUser($inputArr){
        //dd($inputArr);
        $otp = $this->generateOtp();
        $inputArr['verification_otp'] = $otp;
        $inputArr['is_verified'] = 1;
        return self::create($inputArr);
    }

    public function generateOtp(){
        $otp = mt_rand(100000,999999);
        // $otp = 123456;
        $count = self::where('verification_otp', $otp)->count();
        if($count > 0){
            $this->generateOtp();
        }
        return $otp;
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param NULL
     * @return Array of all users
     */
    public function getAllClients(){
        return self::where('role', '0')->where('email_verified_at','!=','null')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param NULL
     * @return Array of all users
     */
    public function getAllTherapists(){
        return self::where('role','1')->where('email_verified_at','!=','null')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param user id
     * @return user object
     */
    public function getUserById($id){
        return self::where('id', $id)->first();
    }

    public function getUserNameId($id){

        $user = self::where('id', $id)->first();
        return ($user) ? $user->first_name.' '.$user->last_name : (null);
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param user id , fields array
     * @return updated
     */
    public function updateUser($id, $inputArr){
        if(isset($inputArr['image']) && $inputArr['image']){
            $image = $inputArr['image'];
            $imageUrl = $this->uploadProfileImage($image);
            $inputArr['image'] = $imageUrl;

            if(isset($inputArr['old_image']) && $inputArr['old_image'] && $imageUrl){
                $oldImageName = basename($inputArr['old_image']);
                Storage::disk('profile-images')->delete($oldImageName);
            }
        } else {
            unset($inputArr['image']);
        }
        unset($inputArr['old_image']);
        
        return self::where('id', $id)->update($inputArr);
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param null
     * @return array of formated user details
     */
    public function getResponseArr(){
      
       
        $userLangauages = $this->userLanguages;
        $languageArr = array();
        foreach ($userLangauages as $key => $userLangauage) {
            if(!$userLangauage->language)
                continue;
            
            $languageArr[] = [
                'id' => optional($userLangauage->language)->id,
                'title' => optional($userLangauage->language)->title
            ];
        }

        // get user specialism
        $userTherapistTypes = $this->userTherapistTypes;
        $therapistTypesArr = array();
        foreach ($userTherapistTypes as $key => $userTherapistType) {
            if(!$userTherapistType->therapistType)
                continue;
            
            $therapistTypesArr[] = [
                'id' => optional($userTherapistType->therapistType)->id,
                'title' => optional($userTherapistType->therapistType)->title
            ];
        }
        
        $therapistProfile = $this->therapistProfile;
        $returnArr = [
            'user_id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'role' => $this->role,
            'languages' => $languageArr,
            'specialism' => $therapistTypesArr,
            'address' => ($therapistProfile) ? ($therapistProfile->address) : (null),
            'latitude' => ($therapistProfile) ? ($therapistProfile->latitude) : (null),
            'longitude' => ($therapistProfile) ? ($therapistProfile->longitude) : (null),
            'experience' => ($therapistProfile) ? ($therapistProfile->experience) : (null),
            'qualification' => ($therapistProfile) ? ($therapistProfile->qualification) : (null),
            'is_email_verified' => ($this->email_verified_at) ? (true) : (false),
            'online_status' => $this->online_status,
            'notification_status' => $this->notification_status,
            'image' => $this->image,
            'social_token' => $this->social_token,
            'login_type' => $this->login_type,
            'stripe_connect_id' => $this->stripe_connect_id,
            'stripe_id' => $this->stripe_id
        ];
        return $returnArr;
    }

    public function getResponseCalletIdArr(){
      
        $getUserCallerId = CallLogs::where('user_id', $this->id)
        ->orderBy('id', 'DESC')
        ->first();
        $CallerId = $getUserCallerId->caller_id;
        $userLangauages = $this->userLanguages;
        $languageArr = array();
        foreach ($userLangauages as $key => $userLangauage) {
            if(!$userLangauage->language)
                continue;
            
            $languageArr[] = [
                'id' => optional($userLangauage->language)->id,
                'title' => optional($userLangauage->language)->title
            ];
        }

        // get user specialism
        $userTherapistTypes = $this->userTherapistTypes;
        $therapistTypesArr = array();
        foreach ($userTherapistTypes as $key => $userTherapistType) {
            if(!$userTherapistType->therapistType)
                continue;
            
            $therapistTypesArr[] = [
                'id' => optional($userTherapistType->therapistType)->id,
                'title' => optional($userTherapistType->therapistType)->title
            ];
        }
        
        $therapistProfile = $this->therapistProfile;
        $returnArr = [
            'user_id' => $this->id,
            'caller_id' => $CallerId,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'role' => $this->role,
            'languages' => $languageArr,
            'specialism' => $therapistTypesArr,
            'address' => ($therapistProfile) ? ($therapistProfile->address) : (null),
            'latitude' => ($therapistProfile) ? ($therapistProfile->latitude) : (null),
            'longitude' => ($therapistProfile) ? ($therapistProfile->longitude) : (null),
            'experience' => ($therapistProfile) ? ($therapistProfile->experience) : (null),
            'qualification' => ($therapistProfile) ? ($therapistProfile->qualification) : (null),
            'is_email_verified' => ($this->email_verified_at) ? (true) : (false),
            'online_status' => $this->online_status,
            'notification_status' => $this->notification_status,
            'image' => $this->image,
            'social_token' => $this->social_token,
            'login_type' => $this->login_type,
            'stripe_connect_id' => $this->stripe_connect_id,
            'stripe_id' => $this->stripe_id
        ];
        return $returnArr;
    }

        /**
     * Upload product image.
     *
     * @param  image  $image
     */
    public function uploadProfileImage($image)
    {
        $fileName   = time() . '.' . $image->getClientOriginalExtension();            
        Storage::disk('profile-images')->putFileAs('/', $image, $fileName);
        return Storage::disk('profile-images')->url($fileName);
    }

    public static function monthly()
    {
        $date = new \DateTime(date('Y-m'));

        $date->modify('-12 months');

        $count = [];
        for ($i = 1; $i <= 12; $i ++) {
            $date->modify('+1 months');
            $month = $date->format('Y-m');

            $userCount = self::where('role', '=', User::CLIENT_ROLE)
                        ->where('email_verified_at', '!=', 'null')->where('created_at','like','%' . $month . '%')->count();
            $therapists = self::where('role', '=', User::THERAPIST_ROLE)
                        ->where('email_verified_at', '!=', 'null')->where('created_at','like','%' . $month . '%')->count();
             $payments = PaymentDetails::where('is_captured',2)
                        ->where('created_at','like','%' . $month . '%')->sum('amount');

            $count['month'][$i] = $month;
            $count['users'][$i] = $userCount;
            $count['therapists'][$i] = $therapists;
            $count['payments'][$i] = (int) $payments;

        }
        return $count;
    }
}
