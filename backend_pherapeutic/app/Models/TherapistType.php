<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapistType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param NULL
     * @return Array of all users
     */
    public function getAllTherapistTypes(){
        return self::orderBy('title', 'ASC')->get();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @var array of user input details
     * @return object of user
     * This function use to save new uer's detail in Database
     */
    public function saveNewTherapistType($inputArr){
        return self::create($inputArr);
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param user id
     * @return user object
     */
    public function getTherapistTypeById($id){
        return self::where('id', $id)->first();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param user id , fields array
     * @return updated
     */
    public function updateTherapistType($id, $inputArr){
        return self::where('id', $id)->update($inputArr);
    }

    public static function getTherapistTypesDropdownArr(){
        return self::orderBy('title', 'asc')->pluck('title', 'id')->toArray();
    }

    public function getResponseArr(){
        $returnArr = [
            'id' => $this->id,
            'title' => $this->title,
            'score' => $this->score
        ];
        return $returnArr;
    }

    /**
     * Get all of the therapist for the therapistsType.
     */
    public function userTherapistType()
    {
        return $this->hasMany('App\Models\UserTherapistType');
    }

    public function searchTherapist($userPoints, $latitude, $longitude){
        //To Do check from appoinment use in this query
        $userTherapistTypeColl = self::select('user_therapist_types.*', 'users.*', 'therapist_profiles.*', 'appointments.status as appointmentStatus')
                                    ->rightJoin('user_therapist_types', 'user_therapist_types.therapist_type_id', '=', 'therapist_types.id')
                                    ->join('users', 'users.id', '=', 'user_therapist_types.user_id')
                                    ->join('therapist_profiles', 'therapist_profiles.user_id', '=', 'user_therapist_types.user_id')
                                    ->leftJoin('appointments', 'appointments.therapist_id', '=', 'users.id')
                                    // ->join('appointments', function ($join) {
                                    //         $join->on('appointments.therapist_id', '=', 'users.id')
                                    //             ->whereIn('appointments.status', ['2', '3', '4']);
                                    //         })
                                    ->where('therapist_types.min_point','<=', $userPoints)
                                    ->where('therapist_types.point','>=', $userPoints)
                                    ->where('users.online_status', '1')
                                    ->selectSub('(111.111 *
                                        DEGREES(ACOS(LEAST(1.0, COS(RADIANS(therapist_profiles.latitude))
                                            * COS(RADIANS('.$latitude.'))
                                            * COS(RADIANS(therapist_profiles.longitude - '.$longitude.'))
                                            + SIN(RADIANS(therapist_profiles.latitude))
                                            * SIN(RADIANS('.$latitude.'))))))','distance_in_km')
                                    ->orderBy('distance_in_km','ASC')
                                    ->get();

        return $userTherapistTypeColl;
    }
}
