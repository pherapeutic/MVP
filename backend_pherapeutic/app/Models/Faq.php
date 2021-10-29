<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Faq extends Model
{

    protected $table = 'faq';
    protected $fillable = [
        'questions', 'answers','type_id'
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

    public function getUserType(){

        $list = [
            User::CLIENT_ROLE=>"Client",
            User::THERAPIST_ROLE=>"Therapist"
        ];

        return isset($list[$this->type_id])?$list[$this->type_id]:"Not Defined";
    }
	
	public function getResponseArr(){
	  return	$returnArr = [
				'id' => $this->id,
				'questions' => mb_convert_encoding($this->questions, 'UTF-8', 'UTF-8'),
				'answers' => mb_convert_encoding($this->answers, 'UTF-8', 'UTF-8'),
				'type_id'=>$this->type_id
				
				
			];
		
	}
	
}
