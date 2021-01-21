<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTherapistType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function therapistType()
    {
        return $this->belongsTo('App\Models\TherapistType', 'therapist_type_id', 'id');
    }

    public function saveNewUserTherapistTypes($inputArr){
        return self::create($inputArr);
    }
    
    public function therapist()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

}
