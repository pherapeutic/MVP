<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackNotes extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'appointment_id',
        'feedback_note',
        'feedback_by',
    ];

    /**
     * Created By Parmod Kumar
     * Created At 03-11-2020
     * @param NULL
     * @return Array of all rating
     */
    public function getAllFeedbacks(){
        return self::all();
    }

    /**
     * Created By Parmod Kumar
     * Created At 03-11-2020
     * @var array of rating input details
     * @return object of rating
     * This function use to save new uer's detail in Database
     */
    public function saveNewFeedback($inputArr){
        return self::create($inputArr);
    }

    public function getRatingByAppointmentId($appointment_id){
        return self::where('appointment_id', $appointment_id)->first();
    }

    public function updateUserFeedback($appointment_id, $inputArr){
        return self::where('appointment_id', $appointment_id)->update($inputArr);
    }
}
