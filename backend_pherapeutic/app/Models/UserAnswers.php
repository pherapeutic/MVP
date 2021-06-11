<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Answers;

class UserAnswers extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'question_id',
        'answer_id',
        'points',
    ];

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param NULL
     * @return Array of all users
     */
    public function getAllUserAnswers(){
        return self::all();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @var array of user input details
     * @return object of user
     * This function use to save new uer's detail in Database
     */
    public function saveNewUserAnswer($inputArr){
        $answerObj = Answers::where('id',$inputArr['answer_id'])->first();
        $inputArr['points'] = $answerObj->point;
        return self::create($inputArr);
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param user id
     * @return user object
     */
    public function getUserAnswerById($id){
        return self::where('id', $id)->first();
    }
   
    /**
     * Created By Ak Tiwari
     * Created At 24-11-2020
     * @param user id
     * @return answer object
     */
    public function getUserAnswerByUserId($id){
        return self::where('user_id', $id)->get()->all();
    }

    public function getQuestion()
    {
        return $this->belongsTo('App\Models\Questions', 'question_id', 'id');
    }

    public function getAnswer()
    {
        return $this->belongsTo('App\Models\Answers', 'answer_id', 'id');
    }
   
}
