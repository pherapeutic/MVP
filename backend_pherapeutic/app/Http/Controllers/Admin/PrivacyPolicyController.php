<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TermsConditionsRequest;
use App\Http\Requests\UpdateTermsConditionsRequest;
use App\Models\PrivacyPolicy;

class PrivacyPolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, PrivacyPolicy $privacyPolicy)
    {
        if ($request->ajax()) {
            $privacyColl = $privacyPolicy->getAllPolicy();
            
            return datatables()->of($privacyColl)
                ->addIndexColumn()
                ->addColumn('id', function ($privacyPolicy) {
                   return $privacyPolicy->id;
                })
                ->addColumn('title', function ($privacyPolicy) {
                    return ($privacyPolicy->title) ? ($privacyPolicy->title) : 'N/A';
                })
                ->addColumn('description', function ($privacyPolicy) {
                    return ($privacyPolicy->description) ? ($privacyPolicy->description) : 'N/A';
                })
                ->addColumn('action', function ($privacyPolicy) {
                    $btn = '';
                    $btn = '<a href="privacypolicy/'.$privacyPolicy->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$privacyPolicy->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.policy.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.policy.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, PrivacyPolicy $PrivacyPolicy)
    {
        //dd(10);
        $inputArr['title'] = $request->get('title');
        $inputArr['description'] = $request->get('description');
        $PrivacyPolicy = $PrivacyPolicy->saveNewPolicy($inputArr);
        if(!$PrivacyPolicy){
            return redirect()->back()->with('error', 'Unable to add questionnaire. Please try again later.');
        }

        return redirect()->route('admin.privacypolicy.index')->with('success_message', 'New Privacy & Policy created successfully.');
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
    public function edit($id, PrivacyPolicy $PrivacyPolicy)
    {

        $PrivacyPolicy = $PrivacyPolicy->getPolicyById($id);
        if(!$PrivacyPolicy){
            return redirect()->back()->with('error_message', 'Terms & Conditions does not exist');
        }

        return view('admin.policy.edit', compact('PrivacyPolicy'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, PrivacyPolicy $PrivacyPolicy)
    {
        $PrivacyPolicy = $PrivacyPolicy->getPolicyById($id);
        if(!$PrivacyPolicy){
            return redirect()->back()->with('error_message', 'This Terms & Conditions does not exist');
        }

        $inputArr['title'] = $request->get('title');
        $inputArr['description'] = $request->get('description');
        $hasUpdated = $PrivacyPolicy->updatePolicy($id, $inputArr);
        
        if(!$hasUpdated){
            return redirect()->back()->with('error_message', 'Unable to update Policy. Please try again later.');
        }

        return redirect()->route('admin.privacypolicy.index')->with('success_message', 'updated successfully.');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, PrivacyPolicy $PrivacyPolicy)
    {
        $PrivacyPolicy = $PrivacyPolicy->getPolicyById($id);

        if(!$PrivacyPolicy){
            return returnNotFoundResponse('This Terms & Conditions does not exist');
        }

        $hasDeleted = $PrivacyPolicy->delete();
        if($hasDeleted){
            return returnSuccessResponse('Privacy & Policy deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
