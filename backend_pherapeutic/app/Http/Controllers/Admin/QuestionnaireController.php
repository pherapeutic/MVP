<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Questions;
use App\Models\Answers;
use App\Models\UserLanguage;
use Validator;

class QuestionnaireController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, Questions $questions)
    {
        if ($request->ajax()) {
            $questionColl = $questions->getAllQuestions();

            return datatables()->of($questionColl)
                ->addIndexColumn()
                ->addColumn('id', function ($question) {
                return $question->id;
                })
                ->addColumn('title', function ($question) {
                    return ($question->title) ? ($question->title) : 'N/A';
                })
                ->addColumn('status', function ($question) {
                    $status = 'N/A';
                    if($question->status == 1){
                        $status = 'Active';
                    }else{
                        $status = 'In-Active';
                    }
                    return $status;
                })
                ->addColumn('ordering', function ($question) {
                    return '<span class="ordering">'.$question->ordering.'</span><input type="number" class="d-none order-value" min="1" data-id="'.$question->id.'">';
                })
                ->addColumn('action', function ($question) {
                    $btn = '';
                    $btn = '<a href="questionnaire/'.$question->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$question->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action','ordering'])
                ->make(true);
        }
        return view('admin.questionnaire.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.questionnaire.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
			'ordering'=>'required|unique:questions,ordering'
        ];

        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $answer = array_filter($input['more']['answer']);
        $point = array_filter($input['more']['point']);
        $combineData = array_combine($answer,$point);

        if(!$answer || !$point){
            return redirect()->back()->with('error_message', 'Unable to create new question. Please enter answer and points.');
        }

        $questionArr['title'] = $input['title'];
        $questionArr['ordering'] = $input['ordering'];
        $questionArr['status'] = '1';
        //dd($questionArr);
        $hasSave = Questions::create($questionArr);

        if(!$hasSave){
            return redirect()->back()->with('error', 'Unable to create new question. Please try again later.');
        }

        foreach ($combineData as $answer => $point) {
            $data = [
                'question_id' => $hasSave->id,
                'title' => $answer,
                'point' => $point
            ];
            $saveAns = Answers::create($data);
        }

        return redirect()->route('admin.questionnaire.index')->with('success_message', 'New question created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Questions $questions)
    {

        $questionObj = $questions->getQuestionById($id);
        if(!$questionObj){
            return redirect()->back()->with('error', 'Question does not exist');
        }

        $questionAnswers = $questionObj->answers;
        return view('admin.questionnaire.edit', compact('questionObj', 'questionAnswers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, Questions $questions)
    {
        $rules = [
            'title' => 'required',
        ];

        $input = $request->all();
		$questionObj = $questions->getQuestionById($id);
		
		 if($input['ordering']==$questionObj->ordering){
		    $rules['ordering']='required';
	    }else{
			 $rules['ordering']='required|unique:questions,ordering';
		}
	   
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
       
	  
 

        
        if(!$questionObj){
            return redirect()->back()->with('error', 'This question does not exist');
        }

        $inputArr = $request->except(['_token', '_method']);
        $questionArr = [
            'title' => $inputArr['title'],
            'ordering' => $inputArr['ordering'],
        ];
        $answer = array_filter($inputArr['more']['answer']);
        $point = array_filter($inputArr['more']['point']);
        $combineData = array_combine($answer,$point);
        
        if(empty($answer) || empty($point)){
            return redirect()->back()->with('error_message', 'Unable to update question. Please enter answer and points.');
        }

        $hasUpdated = $questions->updateQuestion($id, $questionArr);
        if(!$hasUpdated){
            return redirect()->back()->with('error_message', 'Unable to update question. Please try again later.');
        }

        $hasDelete = Answers::where('question_id', $id)->delete();
        // if(!$hasDelete){
            // return redirect()->back()->with('error_message', 'Unable to update answer. Please try again later.');            
        // }

        foreach ($combineData as $answer => $point) {
            $data = [
                'question_id' => $id,
                'title' => $answer,
                'point' => $point
            ];
            $saveAns = Answers::create($data);
        }

        return redirect()->route('admin.questionnaire.index')->with('success_message', 'Question updated successfully.');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Questions $questions)
    {
        $questionObj = $questions->getQuestionById($id);
        if(!$questionObj){
            return returnNotFoundResponse('This question does not exist');
        }

        $hasDeleted = $questionObj->delete();
        if($hasDeleted){
            $questionObj->answers()->delete();
            return returnSuccessResponse('Question deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }

    public function ordering(Request $request, $id, Questions $questions)
    {
        $input = $request->all();        
        $questionObj = $questions->getQuestionById($input['questionId']);
        if(!$questionObj){
            return returnNotFoundResponse('This question does not exist');
        }

        //re arrange the ordering
        $getQuestionOrder = Questions::where('ordering', $id)->first();
        if($getQuestionOrder){
            
            $questionArr = [
                'ordering' => $questionObj->ordering
            ];        
            $hasUpdated = $questions->updateQuestion($getQuestionOrder->id, $questionArr);
        }

        $questionArr = [
            'ordering' => $id
        ];        
        $hasUpdated = $questions->updateQuestion($input['questionId'], $questionArr); 
        return returnSuccessResponse('Question order change successfully');
    }
	 /**
     * destroyAnswerById the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyAnswerById($id){
		$res=Answers::where('id',$id)->delete();
        if($res){
            return returnSuccessResponse('Answer deleted successfully');
        }else{
			return returnNotFoundResponse('This Answers does not exist');
		}
	}

}
