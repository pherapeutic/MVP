<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserAnswers;
use App\Models\Questions;
use Validator;
class QuestionareController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function getQuestions(Questions $questions){
        $questionObj = $questions->getAllQuestions();
        $returnArr = array();
        foreach ($questionObj as $question) {
            $getResponseData = $question->getResponseArr();
            array_push($returnArr, $getResponseData);
        }
        if(!empty($returnArr)){
            return returnSuccessResponse('Get Questionare',$returnArr);
        }else{
            return returnNotFoundResponse('Not found');   
        }
    }

    public function postAnswers(Request $request, UserAnswers $userAnswers){
        $rules = [
            'user_id' => 'required',
            'question_id' => 'required',
            'answer_id' => 'required',
            //'points' => 'required',
        ];
        $userObj = $this->request->user();
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);

        if ($validator->fails()) {
            $validateerror = $validator->errors()->all();
            return $this->validationErrorResponse($validateerror[0]);
        }else{
            
            $respone = $userAnswers->saveNewUserAnswer($inputArr);
            if($respone){
                return returnSuccessResponse('Thanks for answer !');
            }else{
                return returnNotFoundResponse('Something wrong');
           }
        }    
    }

}
