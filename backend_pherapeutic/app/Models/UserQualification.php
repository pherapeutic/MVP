<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQualification extends Model
{
    use HasFactory;
	/**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
       
        'id',
        'user_id',
        'qualification_id',
		
              
    ];
	
	 public function qualification()
    {
        return $this->belongsTo('App\Models\Qualification', 'qualification_id', 'id');
    }
	
	public function saveNewUserQualification($inputArr){
        //dd($inputArr);
        return self::create($inputArr);
    }

    public function getUserQualificationById($user_id){
        return self::where('user_id', $user_id)->first();
    }
}
