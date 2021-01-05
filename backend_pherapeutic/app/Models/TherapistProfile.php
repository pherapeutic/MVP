<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapistProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'address',
        'latitude',
        'longitude',
        'experience',
        'specialism',
        'qaulification',
    ];

    /**
     * Get the user that owns the phone.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }


    public function saveTherapistProfile($inputArr){
        return self::create($inputArr);
    }
    
    public function getTherapistProfileById($user_id){
        return self::where('user_id', $user_id)->first();
    }

    /**
     * Created By Parmod Kumar
     * Created At 02-11-2020
     * @param user id , fields array
     * @return updated
     */
    public function updateTherapistProfile($id, $inputArr){
        return self::where('user_id', $id)->update($inputArr);
    }
}
