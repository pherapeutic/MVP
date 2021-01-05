<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answers extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question_id',
        'title',
        'point',
    ];

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param NULL
     * @return Array of all users
     */
    public function getAllAnswers(){
        return self::all();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @var array of user input details
     * @return object of user
     * This function use to save new uer's detail in Database
     */
    public function saveNewAnswer($inputArr){
        return self::create($inputArr);
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param user id
     * @return user object
     */
    public function getAnswerById($id){
        return self::where('id', $id)->first();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param user id , fields array
     * @return updated
     */
    public function updateAnswer($id, $inputArr){
        return self::where('id', $id)->update($inputArr);
    }

    public function getResponseArr(){
        $returnArr = [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'title' => $this->title,
            'point' => $this->point,
        ];
        return $returnArr;
    }
    
}
