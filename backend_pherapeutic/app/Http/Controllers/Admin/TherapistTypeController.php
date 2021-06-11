<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TherapistType;
use Validator;

class TherapistTypeController extends Controller
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
    public function index(Request $request, TherapistType $TTypes)
    {
        if ($request->ajax()) {
            $typesColl = $TTypes->getAllTherapistTypes();
            return datatables()->of($typesColl)
                ->addIndexColumn()
                ->addColumn('id', function ($TTypes) {
                   return $TTypes->id;
                })
                ->addColumn('title', function ($TTypes) {
                    return ($TTypes->title) ? ($TTypes->title) : 'N/A';
                })->addColumn('min_point', function ($TTypes) {
                    return $TTypes->min_point;
                })->addColumn('point', function ($TTypes) {
                    return ($TTypes->point) ? ($TTypes->point) : 'N/A';
                })
                ->addColumn('action', function ($TTypes) {
                    $btn = '';
                    $btn = '<a href="therapisttypes/'.$TTypes->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$TTypes->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.therapisttypes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.therapisttypes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, TherapistType $TTypes)
    {
        //echo"<pre>";print_r($request->all());die;
        $rules = [
            'title' => 'required',
            'min_point'=>'required|numeric|min:0',
            'max_point' => 'required|numeric|gt:min_point|min:0'
        ];


        $input = $request->all();

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $inputArr = $request->except(['_token','max_point']);
            $inputArr['point'] = $request->input('max_point');
            $typesObj = $TTypes->saveNewTherapistType($inputArr);
            if(!$typesObj){
                return redirect()->back()->with('error_message', 'Unable to add therapist type. Please try again later.');
            }

            return redirect()->route('admin.therapisttypes.index')->with('success_message', 'Specialism added successfully.');
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
    public function edit($id,  TherapistType $TTypes)
    {

        $TTypes = $TTypes->getTherapistTypeById($id);
        if(!$TTypes){
            return redirect()->back()->with('error', 'Therapist type does not exist');
        }

        return view('admin.therapisttypes.edit', compact('TTypes'));
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
            'min_point'=>'required|numeric|min:0',
            'max_point' => 'required|numeric|gt:min_point|min:0'
        ];

        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $TTypes = new TherapistType();
            $TTypes = $TTypes->getTherapistTypeById($id);
            if(!$TTypes){
                return redirect()->back()->with('error', 'This therapist type does not exist');
            }

            $inputArr = $request->except(['_token', 'therapisttype_id', '_method','max_point']);
            $inputArr['point'] = $request->input('max_point');
            $hasUpdated = $TTypes->updateTherapistType($id, $inputArr);

            if($hasUpdated){
                return redirect()->route('admin.therapisttypes.index')->with('success', 'Specialism updated successfully.');
            }
            return redirect()->back()->with('error', 'Unable to update Therapist type. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, TherapistType $TTypes)
    {
        $typesObj = $TTypes->getTherapistTypeById($id);

        if(!$typesObj){
            return returnNotFoundResponse('This therapist type does not exist');
        }

        $hasDeleted = $typesObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Specialism deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
