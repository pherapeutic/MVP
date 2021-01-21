<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Softdeletes;
use App\Models\Answers;

class Questions extends Model
{
    use HasFactory, Softdeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //'answer_id',
        'title',
        'status',
    ];

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param NULL
     * @return Array of all users
     */
    public function getAllQuestions(){
        return self::where('status','1')->orderBy('ordering', 'ASC')->get()->all();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @var array of user input details
     * @return object of user
     * This function use to save new uer's detail in Database
     */
    public function saveNewQuestion($inputArr){
        return self::create($inputArr);
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param user id
     * @return user object
     */
    public function getQuestionById($id){
        return self::where('id', $id)->first();
    }

    /**
     * Created By Parmod KUmar
     * Created At 22-10-2020
     * @param user id , fields array
     * @return updated
     */
    public function updateQuestion($id, $inputArr){
        return self::where('id', $id)->update($inputArr);
    }

    public function answers()
    {
        return $this->hasMany('App\Models\Answers', 'question_id');
    }

    public function getResponseArr(){
        $returnArr = [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
        ];
        $answerData = self::getAnswer($returnArr['id']);
        $returnArr['answer'] = $answerData;
        return $returnArr;
    }

    public function getAnswer($questionId){
        $answers = Answers::where('question_id', $questionId)->get()->all();
        $ansReturnArr = array();
        if(!$answers){
            return null;
        }
        foreach ($answers as $answer) {
            array_push($ansReturnArr, $answer->getResponseArr());
        }
        return $ansReturnArr;
    }
}
