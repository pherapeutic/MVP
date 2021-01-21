<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Questions;
use App\Models\Answers;
use Validator;

class QuestionController extends Controller
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
            $questionsColl = $questions->getAllQuestions();
            return datatables()->of($questionsColl)
                ->addIndexColumn()
                ->addColumn('id', function ($questions) {
                   return $questions->id;
                })
                ->addColumn('title', function ($questions) {
                    return ($questions->title) ? ($questions->title) : 'N/A';
                })
                ->addColumn('status', function ($questions) {
                    return ($questions->status) ? ($questions->status) : 'N/A';
                })
                ->addColumn('action', function ($questions) {
                    $btn = '';
                    $btn = '<a href="questions/'.$questions->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$questions->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.questions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $answers = new Answers();
        $answers =$answers->getAllAnswers();
        return view('admin.questions.create',compact('answers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Questions $questions)
    {
        //echo"<pre>";print_r($request->all());die;
        $rules = [
            'title' => 'required',
            'status' => 'required'
        ];


        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $inputArr = $request->except(['_token']);
            $questionsObj = $questions->saveNewQuestion($inputArr);
            if(!$questionsObj){
                return redirect()->back()->with('error', 'Unable to create Question. Please try again later.');
            }

            return redirect()->route('questions.index')->with('success', 'Question created successfully.');
        }
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
    public function edit($id,  Questions $questions)
    {

        $questions = $questions->getQuestionById($id);
        if(!$questions){
            return redirect()->back()->with('error', 'Questions does not exist');
        }

        return view('admin.questions.edit', compact('questions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //echo"<pre>";print_r($request->all());die;
         $rules = [
            'title' => 'required',
            'status' => 'required'
        ];


        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $questions = new Questions();
            $questions = $questions->getQuestionById($id);
            if(!$questions){
                return redirect()->back()->with('error', 'This question does not exist');
            }

            $inputArr = $request->except(['_token', 'question_id', '_method']);
            $hasUpdated = $questions->updateQuestion($id, $inputArr);

            if($hasUpdated){
                return redirect()->route('questions.index')->with('success', 'question updated successfully.');
            }
            return redirect()->back()->with('error', 'Unable to update questions. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Questions $questions)
    {
        $questionsObj = $questions->getQuestionById($id);

        if(!$questionsObj){
            return returnNotFoundResponse('This question does not exist');
        }

        $hasDeleted = $questionsObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Question deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
