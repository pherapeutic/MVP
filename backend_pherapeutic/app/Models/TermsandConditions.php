<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TermsandConditions extends Model
{

    protected $table = 'termsandconditions';
    protected $fillable = [
        'title', 'description'
    ];
    public function saveNewTerms($inputArr){
        return self::create($inputArr);
    }
    public function getAllTerms(){
        return self::all();
    }

    public function getTermsById($id){
        return self::where('id', $id)->first();
    }
    public function updateTerms($id, $inputArr){
        return self::where('id', $id)->update($inputArr);
    }
}
