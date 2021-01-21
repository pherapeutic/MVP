<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PrivacyPolicy extends Model
{

    protected $table = 'privacypolicy';
    protected $fillable = [
        'title', 'description'
    ];

    public function saveNewPolicy($inputArr){
        return self::create($inputArr);
    }
    public function getAllPolicy(){
        return self::all();
    }

    public function getPolicyById($id){
        return self::where('id', $id)->first();
    }
    public function updatePolicy($id, $inputArr){
        return self::where('id', $id)->update($inputArr);
    }
}
