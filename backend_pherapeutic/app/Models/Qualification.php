<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;
	/**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
       
        'id',
        'title',
      ];
	  
	   public function getResponseArr(){
        $returnArr = [
            'id' => $this->id,
            'title' => $this->title
        ];
        return $returnArr;
    }
	
	public function getQualification(){
		  return self::all();
	}	
          
}
