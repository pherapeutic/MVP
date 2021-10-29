<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

     public function index(Request $request, Faq $faqs)
    {
        if ($request->ajax()) {
            $faqsColl = $faqs->getAllFaqs();
            return datatables()->of($faqsColl)
                ->addIndexColumn()
                ->addColumn('id', function ($faqs) {
                   return $faqs->id;
                })
                ->addColumn('question', function ($faqs) {
                    return ($faqs->questions) ? ($faqs->questions) : 'N/A';
                })
                ->addColumn('answer', function ($faqs) {
                    return ($faqs->answers) ? ($faqs->answers) : 'N/A';
                })
                 ->addColumn('type_id', function ($faqs) {
                    return $faqs->getUserType();
                })
                ->addColumn('action', function ($faqs) {
                    $btn = '';
                    $btn = '<a href="faq/'.$faqs->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$faqs->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.faq.index');
    }

    public function create()
    {
        return view('admin.faq.create');
    }

    public function store(Request $request, Faq $faq)
    {
        //dd(10);
        $inputArr['questions'] = $request->get('questions');
        $inputArr['answers'] = $request->get('answers');
        $inputArr['type_id'] = $request->get('type_id');
        $faq = $faq->saveNewFaq($inputArr);
        if(!$faq){
            return redirect()->back()->with('error', 'Unable to add faq. Please try again later.');
        }

        return redirect()->route('admin.faq.index')->with('success_message', 'New FAQ created successfully.');
    }


     public function edit($id)
    {
        $faq = new Faq;

        $faq = $faq->getFaqById($id);
        if(!$faq){
            return redirect()->back()->with('error_message', 'FAQ does not exist');
        }

        return view('admin.faq.edit', compact('faq'));
    }

     public function update($id,Request $request)
    {
        $faq = new FAQ;
        $faq = $faq->getFaqById($id);
        if(!$faq){
            return redirect()->back()->with('error_message', 'This FAQ does not exist');
        }

        $inputArr['questions'] = $request->get('questions');
        $inputArr['answers'] = $request->get('answers');
        $inputArr['type_id'] = $request->get('type_id');
        $hasUpdated = $faq->updateFaq($id, $inputArr);
        
        if(!$hasUpdated){
            return redirect()->back()->with('error_message', 'Unable to update FAQ. Please try again later.');
        }

        return redirect()->route('admin.faq.index')->with('success_message', 'updated successfully.');
        
    }

     public function destroy($id)
    {
        $faq = new Faq;
        $faq = $faq->getFaqById($id);

        if(!$faq){
            return returnNotFoundResponse('This FAQ does not exist');
        }

        $hasDeleted = $faq->delete();
        if($hasDeleted){
            return returnSuccessResponse('FAQ deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }


}
