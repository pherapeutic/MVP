<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLanguages extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'language_id',
    ];

	public function saveNewUserLanguages($inputArr){
        return self::create($inputArr);
    }
    public function getUserLanguagesById($user_id){
        return self::where('user_id', $user_id)->first();
    }

    /**
     * Created By Parmod Kumar
     * Created At 02-11-2020
     * @param user id , fields array
     * @return updated
     */
    public function updateUserLanguages($user_id, $inputArr){
        return self::where('user_id', $user_id)->update($inputArr);
    }
}
