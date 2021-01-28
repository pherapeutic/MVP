<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Faq extends Model
{

    protected $table = 'faq';
    protected $fillable = [
        'questions', 'answers'
    ];

     public function getAllFaqs(){
        return self::all();
    }

     public function saveNewFaq($inputArr){
        return self::create($inputArr);
    }

     public function getFaqById($id){
        return self::where('id', $id)->first();
    }
     public function updateFaq($id, $inputArr){
        return self::where('id', $id)->update($inputArr);
    }
}
