<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'image',
        'stripe_id',
        'is_pro_bono_work',
        'role',
        "temp_email",
        "email_verified_at",
        "verify_temp_email_token",
        'verification_otp',
        'reset_password_token',
        'fcm_token',
        'device_type',
        'notification_status',
        'online_status',
    ];

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

     /**
     * Use mutator to convert password into hash while creating new club
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }


    /**
     * Get the phone record associated with the user.
     */
    public function therapistprofile()
    {
        return $this->hasOne('App\Models\TherapistProfile','user_id');
    }

    public function generateOtp(){
        // $otp = mt_rand(100000,999999);
        $otp = 123456;
        // $count = self::where('verification_otp', $otp)->count();
        // if($count > 0){
        //     $this->generateOtp();
        // }
        return $otp;
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param NULL
     * @return Array of all users
     */
    public function getAllUsers(){
        return self::all();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param NULL
     * @return Array of all users
     */
    public function getAllClients(){
        return self::where('role','Client')->get();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param NULL
     * @return Array of all users
     */
    public function getAllTherapists(){
        return self::where('role','Therapist')->get();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @var array of user input details
     * @return object of user
     * This function use to save new uer's detail in Database
     */
    public function saveNewUser($inputArr){
        $otp = $this->generateOtp();
        $inputArr['verification_otp'] = $otp;
        return self::create($inputArr);
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
        return self::where('id', $id)->update($inputArr);
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param null
     * @return array of formated user details
     */
    public function getResponseArr(){
        $returnArr = [
            'user_id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'image' => $this->image,
            'is_email_verified' =>($this->email_verified_at) ? ('1') : ('0'),
        ];
        return $returnArr;
    }
}
