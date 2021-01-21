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

}
