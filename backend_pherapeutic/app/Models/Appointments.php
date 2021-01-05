<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointments extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'therapist_id',
        // 'user_feedback',
        // 'therapist_feedback',
        // 'rating',
        'status',
        'is_trail',
        'ended_at',
    ];

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param NULL
     * @return Array of all users
     */
    public function getAllAppointments(){
        return self::all();
    }

    public function getAllTherapistAppointments($id){
        return self::where('therapist_id', $id)->get();
    }

    public function getAllClientAppointments($id){
        return self::where('user_id', $id)->get();
    }

    public function getTherapistAppointmentRequests($therapist_id, $status){
        return self::where('therapist_id', $therapist_id)->where('status', $status)->get();
    }

    public function getResponseArr(){
        $returnArr = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'therapist_id' => $this->therapist_id,
            'status' => $this->status,
            'is_trail' => $this->is_trail,
            'ended_at' => $this->ended_at
        ];
        return $returnArr;
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @var array of user input details
     * @return object of user
     * This function use to save new uer's detail in Database
     */
    public function saveNewAppointment($inputArr){
        return self::create($inputArr);
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param user id
     * @return user object
     */
    public function getAppointmentById($id){
        return self::where('id', $id)->first();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param user id , fields array
     * @return updated
     */
    public function updateAppointment($id, $inputArr){
        return self::where('id', $id)->update($inputArr);
    }
    
    public function therapist()
    {
        return $this->belongsTo('App\Models\User', 'therapist_id', 'id');
    }
    
    public function rating()
    {
        return $this->hasMany('App\Models\Rating', 'appointment_id', 'id');
    }
   
    public function feedbackNotes()
    {
        return $this->hasMany('App\Models\FeedbackNotes', 'appointment_id', 'id');
    }
   
}
