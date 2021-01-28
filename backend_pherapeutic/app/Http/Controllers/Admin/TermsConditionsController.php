<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\TermsConditionsRequest;
use App\Http\Requests\UpdateTermsConditionsRequest;
use App\Http\Controllers\Controller;
use App\Models\TermsandConditions;

class TermsConditionsController extends Controller
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
    public function index(Request $request, TermsandConditions $termsandconditions)
    {
        if ($request->ajax()) {
            $termsColl = $termsandconditions->getAllTerms();
            
            return datatables()->of($termsColl)
                ->addIndexColumn()
                ->addColumn('id', function ($termsandconditions) {
                   return $termsandconditions->id;
                })
                ->addColumn('title', function ($termsandconditions) {
                    return ($termsandconditions->title) ? ($termsandconditions->title) : 'N/A';
                })
                ->addColumn('description', function ($termsandconditions) {
                    return ($termsandconditions->description) ? (html_entity_decode(strip_tags($termsandconditions->description))) : 'N/A';
                })
                ->addColumn('action', function ($termsandconditions) {
                    $btn = '';
                    $btn = '<a href="termsandconditions/'.$termsandconditions->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$termsandconditions->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.termsandconditions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.termsandconditions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TermsConditionsRequest $request, TermsandConditions $termsandconditions)
    {
        //dd(10);
        $inputArr['title'] = $request->get('title');
        $inputArr['description'] = $request->get('description');
        $termsandconditionsObj = $termsandconditions->saveNewTerms($inputArr);
        if(!$termsandconditionsObj){
            return redirect()->back()->with('error', 'Unable to add questionnaire. Please try again later.');
        }

        return redirect()->route('admin.termsandconditions.index')->with('success_message', 'New terms & conditions created successfully.');
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
    public function edit($id, TermsandConditions $termsandconditions)
    {

        $termsandconditionsObj = $termsandconditions->getTermsById($id);
        if(!$termsandconditionsObj){
            return redirect()->back()->with('error_message', 'Terms & Conditions does not exist');
        }

        return view('admin.termsandconditions.edit', compact('termsandconditionsObj'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdateTermsConditionsRequest $request, TermsandConditions $termsandconditions)
    {
        $termsandconditionsObj = $termsandconditions->getTermsById($id);
        if(!$termsandconditionsObj){
            return redirect()->back()->with('error_message', 'This Terms & Conditions does not exist');
        }

        $inputArr['title'] = $request->get('title');
        $inputArr['description'] = $request->get('description');
        $hasUpdated = $termsandconditions->updateTerms($id, $inputArr);
        
        if(!$hasUpdated){
            return redirect()->back()->with('error_message', 'Unable to update Terms. Please try again later.');
        }

        return redirect()->route('admin.termsandconditions.index')->with('success_message', 'Terms detail updated successfully.');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, TermsandConditions $termsandconditions)
    {
        $termsandconditionsObj = $termsandconditions->getTermsById($id);

        if(!$termsandconditionsObj){
            return returnNotFoundResponse('This Terms & Conditions does not exist');
        }

        $hasDeleted = $termsandconditionsObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Terms & Conditions deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
