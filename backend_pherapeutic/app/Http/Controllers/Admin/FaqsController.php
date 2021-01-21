<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Faqs;
use Validator;

class FaqsController extends Controller
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
    public function index(Request $request, Faqs $faqs)
    {
        if ($request->ajax()) {
            $faqsColl = $faqs->getAllFaqs();
            return datatables()->of($faqsColl)
                ->addIndexColumn()
                ->addColumn('id', function ($faqs) {
                   return $faqs->id;
                })
                ->addColumn('question', function ($faqs) {
                    return ($faqs->question) ? ($faqs->question) : 'N/A';
                })
                ->addColumn('answer', function ($faqs) {
                    return ($faqs->answer) ? ($faqs->answer) : 'N/A';
                })
                ->addColumn('action', function ($faqs) {
                    $btn = '';
                    $btn = '<a href="faqs/'.$faqs->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$faqs->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.faqs.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.faqs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Faqs $faqs)
    {
        $rules = [
            'question' => 'required',
            'answer' => 'required'
        ];


        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            //return $this->validationErrorResponse();
           // echo"<pre>";print_r($validator->errors()->all());die;
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $inputArr = $request->except(['_token']);
            $faqsObj = $faqs->saveNewFaq($inputArr);
            if(!$faqsObj){
                return redirect()->back()->with('error', 'Unable to add questionnaire. Please try again later.');
            }

            return redirect()->route('faqs.index')->with('success', 'Questionnaire added successfully.');
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
    public function edit($id, Faqs $faqs)
    {

        $faqs = $faqs->getFaqById($id);
        if(!$faqs){
            return redirect()->back()->with('error', 'Questionnaire does not exist');
        }

        return view('admin.faqs.edit', compact('faqs'));
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
            'question' => 'required',
            'answer' => 'required'
        ];


        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{

            $faqs = new Faqs();
            $faqs = $faqs->getFaqById($id);
            if(!$faqs){
                return redirect()->back()->with('error', 'This Questionnaire does not exist');
            }

            $inputArr = $request->except(['_token', 'faq_id', '_method']);
            $hasUpdated = $faqs->updateFaq($id, $inputArr);

            if($hasUpdated){
                return redirect()->route('faqs.index')->with('success', 'Questionnaire updated successfully.');
            }
            return redirect()->back()->with('error', 'Unable to update Questionnaire. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Faqs $faqs)
    {
        $faqsObj = $faqs->getFaqById($id);

        if(!$faqsObj){
            return returnNotFoundResponse('This Questionnaire does not exist');
        }

        $hasDeleted = $faqsObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Questionnaire deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
