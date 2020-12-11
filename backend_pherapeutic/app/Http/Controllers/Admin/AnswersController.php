<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Answers;
use App\Models\Questions;
use Validator;

class AnswersController extends Controller
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
    public function index(Request $request, Answers $answers)
    {
        if ($request->ajax()) {
            $answersColl = $answers->getAllAnswers();
            return datatables()->of($answersColl)
                ->addIndexColumn()
                ->addColumn('id', function ($answers) {
                   return $answers->id;
                })
                ->addColumn('title', function ($answers) {
                    return ($answers->title) ? ($answers->title) : 'N/A';
                })
                ->addColumn('action', function ($answers) {
                    $btn = '';
                    $btn = '<a href="answers/'.$answers->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$answers->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.answers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $questions = new Questions();
        $questions =$questions->getAllQuestions();
        return view('admin.answers.create', compact('questions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Answers $answers)
    {
        //echo"<pre>";print_r($request->all());die;
        $rules = [
            'title' => 'required',
            //'status' => 'required'
        ];


        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $inputArr = $request->except(['_token']);
            $answersObj = $answers->saveNewAnswer($inputArr);
            if(!$answersObj){
                return redirect()->back()->with('error', 'Unable to create Answer. Please try again later.');
            }

            return redirect()->route('answers.index')->with('success', 'Answer created successfully.');
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
    public function edit($id,  Answers $answers)
    {
        $questions = new Questions();
        $questions =$questions->getAllQuestions();
        $answers = $answers->getAnswerById($id);
        if(!$answers){
            return redirect()->back()->with('error', 'Answer does not exist');
        }

        return view('admin.answers.edit', compact('answers','questions'));
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
            //'status' => 'required'
        ];


        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $answers = new Answers();
            $answers = $answers->getAnswerById($id);
            if(!$answers){
                return redirect()->back()->with('error', 'This answer does not exist');
            }

            $inputArr = $request->except(['_token', 'answer_id', '_method']);
            $hasUpdated = $answers->updateAnswer($id, $inputArr);

            if($hasUpdated){
                return redirect()->route('answers.index')->with('success', 'answer updated successfully.');
            }
            return redirect()->back()->with('error', 'Unable to update answer. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Answers $answers)
    {
        $answersObj = $answers->getAnswerById($id);

        if(!$answersObj){
            return returnNotFoundResponse('This answer does not exist');
        }

        $hasDeleted = $answersObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Answer deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
