<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Languages;
use Validator;

class LanguagesController extends Controller
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
    public function index(Request $request, Languages $languages)
    {
        if ($request->ajax()) {
            $languagesColl = $languages->getAllLanguages();
            return datatables()->of($languagesColl)
                ->addIndexColumn()
                ->addColumn('id', function ($languages) {
                   return $languages->id;
                })
                ->addColumn('title', function ($languages) {
                    return ($languages->title) ? ($languages->title) : 'N/A';
                })
                ->addColumn('action', function ($languages) {
                    $btn = '';
                    $btn = '<a href="languages/'.$languages->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$languages->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.languages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.languages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Languages $languages)
    {
        //echo"<pre>";print_r($request->all());die;
        $rules = [
            'title' => 'required'
        ];


        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $inputArr = $request->except(['_token']);
            $languagesObj = $languages->saveLanguages($inputArr);
            if(!$languagesObj){
                return redirect()->back()->with('error', 'Unable to add language. Please try again later.');
            }

            return redirect()->route('languages.index')->with('success', 'Language account added successfully.');
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
    public function edit($id,  Languages $languages)
    {

        $languages = $languages->getLanguageById($id);
        if(!$languages){
            return redirect()->back()->with('error', 'Language does not exist');
        }

        return view('admin.languages.edit', compact('languages'));
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
            'title' => 'required'
        ];

        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $languages = new Languages();
            $languages = $languages->getLanguageById($id);
            if(!$languages){
                return redirect()->back()->with('error', 'This language does not exist');
            }

            $inputArr = $request->except(['_token', 'language_id', '_method']);
            $hasUpdated = $languages->updateLanguage($id, $inputArr);

            if($hasUpdated){
                return redirect()->route('languages.index')->with('success', 'Language type updated successfully.');
            }
            return redirect()->back()->with('error', 'Unable to update language. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Languages $languages)
    {
        $languagesObj = $languages->getLanguageById($id);

        if(!$languagesObj){
            return returnNotFoundResponse('This language does not exist');
        }

        $hasDeleted = $languagesObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('language deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
