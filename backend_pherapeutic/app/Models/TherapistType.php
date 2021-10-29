<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CallLogs;

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

    public function getCallLogCount($id){

        return CallLogs::where(['user_id'=>$id,'therapist_id'=>$this->id])->count();
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

    public function searchTherapistList($userPoints, $latitude, $longitude,$default){
        //To Do check from appoinment use in this query
        // $user_id=2;
        //$therapist_id =DB::table('')->pluck('id')->toArray();
        // $query = self::join('user_languages','languages.id','=','user_languages.language_id')
        //             ->join('call_logs','call_logs.id','=','user_languages.user_id')
        //             join('user_languages','languages.id','=','user_languages.language_id');

        /*$userTherapistTypeColl=  DB::table('users s')
        ->join('therapist_profiles as th','s.id','=','th.user_id')
        ->join('user_therapist_types as tsy','ty.id','=','tsy.therapist_type_id')
    
        ->join('user_languages as ul','ul.user_id','=','th.user_id')
        ->join('languages as lg','lg.id','=','ul.language_id')
    
        ->join('call_logs as cl','cl.user_id','=','th.user_id')
        ->join('payment_details as py','cl.id','=','py.call_logs_id')
        ->join('ratings as rt','cl.id','=','rt.call_logs_id')
        ->where('th.user_id',107)
        ->select('s.id','th.id',' s.first_name','s.last_name','ty.title','
            tsy.therapist_type_id ','
            ul.language_id','
            lg.title','
            th.address','
            th.latitude','
            th.longitude','
            th.experience','
            th.qualification','
            s.image','
            py.amount','
            py.refund_amount','
            cl.id','
            rt.rating','
            rt.comment')->get();
            */
    
        $userTherapistTypeColl_query = self::select('user_therapist_types.*','therapist_types.title as name',
         'users.*','lg.title','py.amount','py.refund_amount','py.transfer_amount','therapist_profiles.*', 'rt.comment','rt.rating',
        'appointments.status as appointmentStatus')
                                    ->Join('user_therapist_types', 'user_therapist_types.therapist_type_id', '=', 'therapist_types.id')
                                    ->join('users', 'users.id', '=', 'user_therapist_types.user_id')
                                    // ->join('call_logs', 'cl.id', '=', 'py.call_logs_id')
                                    ->join('therapist_profiles', 'therapist_profiles.user_id', '=', 'user_therapist_types.user_id')
                                    ->leftJoin('appointments', 'appointments.therapist_id', '=', 'users.id')
                                    ->join('user_languages as ul','ul.user_id','=','user_therapist_types.user_id')
                                    ->join('languages as lg','lg.id','=','ul.language_id')
                                    ->leftJoin('call_logs as cl','cl.user_id','=','user_therapist_types.user_id')
                                    ->leftJoin('payment_details as py','cl.id','=','py.call_logs_id')
                                    ->leftJoin('ratings as rt','cl.id','=','rt.call_logs_id');
                                    // ->join('appointments', function ($join) {
                                    //         $join->on('appointments.therapist_id', '=', 'users.id')
                                    //             ->whereIn('appointments.status', ['2', '3', '4']);
                                    //         })
                                   
                                    $distance = 10;

                                    if($default !=2){
                                     $userTherapistTypeColl= $userTherapistTypeColl_query->where('therapist_types.min_point','<=', $userPoints)
                                    ->where('therapist_types.point','>=', $userPoints);

                                    }

                                    $userTherapistTypeColl= $userTherapistTypeColl_query->where('users.online_status', '1');
                                     if(!empty($latitude) && !empty($longitude)){
                                        $userTherapistTypeColl = $userTherapistTypeColl->selectSub('( 6371 * acos( cos( radians(' . $latitude . ') ) * cos( radians( therapist_profiles.latitude ) ) * cos( radians( therapist_profiles.longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude .') ) * sin( radians(therapist_profiles.latitude) ) ) )','distance_in_km')->having('distance_in_km', '<', $distance)
                                    ->orderBy('distance_in_km','ASC');
                                        
                                    }
                                    
                                    $userTherapistTypeColl = $userTherapistTypeColl->get();
                                    
                                    //dd($userTherapistTypeColl_query);

        return $userTherapistTypeColl;
    }

    public function getOnlineTherapist(){

         return self::select('user_therapist_types.*','therapist_types.title as name',
         'users.*','lg.title','py.amount','py.refund_amount','py.transfer_amount','therapist_profiles.*', 'rt.comment','rt.rating',
        'appointments.status as appointmentStatus')
                                    ->Join('user_therapist_types', 'user_therapist_types.therapist_type_id', '=', 'therapist_types.id')
                                    ->join('users', 'users.id', '=', 'user_therapist_types.user_id')
                                    // ->join('call_logs', 'cl.id', '=', 'py.call_logs_id')
                                    ->join('therapist_profiles', 'therapist_profiles.user_id', '=', 'user_therapist_types.user_id')
                                    ->leftJoin('appointments', 'appointments.therapist_id', '=', 'users.id')
                                    ->join('user_languages as ul','ul.user_id','=','user_therapist_types.user_id')
                                    ->join('languages as lg','lg.id','=','ul.language_id')
                                    ->leftJoin('call_logs as cl','cl.user_id','=','user_therapist_types.user_id')
                                    ->leftJoin('payment_details as py','cl.id','=','py.call_logs_id')
                                    ->leftJoin('ratings as rt','cl.id','=','rt.call_logs_id') ->where('users.online_status', '1')->first();


    }
}


