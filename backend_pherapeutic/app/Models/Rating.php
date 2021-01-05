<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'appointment_id',
        'rating',
        'comment',
    ];

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param NULL
     * @return Array of all rating
     */
    public function getAllRatings(){
        return self::all();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @var array of rating input details
     * @return object of rating
     * This function use to save new uer's detail in Database
     */
    public function saveNewRating($inputArr){
        return self::create($inputArr);
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param rating id
     * @return rating object
     */
    public function getRatingById($id){
        return self::where('id', $id)->first();
    }

    public function getRatingByClientId($appointment_id){
        return self::where('appointment_id', $appointment_id)->first();
    }


    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param rating id , fields array
     * @return updated
     */
    public function updateRating($id, $inputArr){
        return self::where('id', $id)->update($inputArr);
    }
    
    public function updateUserRating($appointment_id, $inputArr){
        return self::where('appointment_id', $appointment_id)->update($inputArr);
    }
}
