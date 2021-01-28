<?php

namespace App\Http\Controllers\Admin\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTherapistRequest;
use App\Http\Requests\UpdateTherapistRequest;
use App\Models\User;
use App\Models\UserLanguage;
use App\Models\UserTherapistType;
use App\Models\TherapistProfile;

class TherapistController extends Controller
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
    public function index(Request $request, User $user)
    {
        if ($request->ajax()) {
            $userColl = $user->getAllTherapists();
            //echo "<pre>"; print_r($usersColl); die;
            return datatables()->of($userColl)
                ->addIndexColumn()
                ->addColumn('id', function ($userObj) {
                return $userObj->id;
                })
                ->addColumn('name', function ($userObj) {
                    return ($userObj->full_name) ? ($userObj->full_name) : 'N/A';
                })
                ->addColumn('email', function ($userObj) {
                    return ($userObj->email) ? ($userObj->email) : 'N/A';
                })
                ->addColumn('languages', function ($userObj) {
                    return ($userObj->getLanguagesString()) ? ($userObj->getLanguagesString()) : 'N/A';
                })
                ->addColumn('specialism', function ($userObj) {
                    return $userObj->getTherapistSpecialisation();
                })
                ->addColumn('experience', function ($userObj) {
                    return (optional($userObj->therapistProfile)->experience) ? ($userObj->therapistProfile->experience.' Year(s)') : 'N/A';
                })
                ->addColumn('action', function ($userObj) {
                    $btn = '';
                    $btn = '<a href="therapist/'.$userObj->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$userObj->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.users.therapist.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.therapist.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTherapistRequest $request, User $user, TherapistProfile $therapistProfile, UserLanguage $userlanguage, UserTherapistType $userTherapistType)
    {
        $inputArr = $request->except(['_token', 'confirm_password']);
        
        $userArr = [
            'first_name' => $inputArr['first_name'],
            'last_name' => $inputArr['last_name'],
            'email' => $inputArr['email'],
            'password' => $inputArr['password'],
            'role' => '1'
        ];
        $userObj = $user->saveNewUser($userArr);
        if(!$userObj){
            return redirect()->back()->with('error', 'Unable to create new therapist. Please try again later.');
        }

        $therapistProfileArr = [
            'user_id' => $userObj->id,
            'address' => ($inputArr['address']) ? ($inputArr['address']) : (null),
            'latitude' => ($inputArr['latitude']) ? ($inputArr['latitude']) : (null),
            'longitude' => ($inputArr['longitude']) ? ($inputArr['longitude']) : (null),
            'experience' => ($inputArr['experience']) ? ($inputArr['experience']) : (null),
            'qualification' => ($inputArr['qualification']) ? ($inputArr['qualification']) : (null),
        ];
        $therapistProfileObj =  $therapistProfile->saveTherapistProfile($therapistProfileArr);

        $languagesArr = $request->get('languages');
        foreach ($languagesArr as $key => $languageId) {
            $userLanguageArr = [
                'user_id' => $userObj->id,
                'language_id' => $languageId
            ];
            $userlanguage->saveNewUserLanguages($userLanguageArr);
        }

        $specialismsArr = $request->get('specialisms');
        foreach ($specialismsArr as $key => $specialismId) {
            $userSpecialismArr = [
                'user_id' => $userObj->id,
                'therapist_type_id' => $specialismId
            ];
            $userTherapistType->saveNewUserTherapistTypes($userSpecialismArr);
        }
        return redirect()->route('admin.therapist.index')->with('success_message', 'New therapist created successfully.');
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
    public function edit($id,  User $user)
    {

        $userObj = $user->getUserById($id);
        if(!$userObj){
            return redirect()->back()->with('error', 'Therapist does not exist');
        }

        $therapistProfile = $userObj->therapistProfile;
        return view('admin.users.therapist.edit', compact('userObj', 'therapistProfile'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdateTherapistRequest $request, User $user, TherapistProfile $therapistProfile, UserLanguage $userlanguage, UserTherapistType $userTherapistType)
    {
        $userObj = $user->getUserById($id);
        if(!$userObj){
            return redirect()->back()->with('error', 'This therapist does not exist');
        }

        $inputArr = $request->except(['_token', '_method']);
        $userArr = [
            'first_name' => $inputArr['first_name'],
            'last_name' => $inputArr['last_name']
        ];
        $hasUpdated = $userObj->updateUser($id, $userArr);

        if(!$hasUpdated){
            return redirect()->back()->with('error_message', 'Unable to update therapist. Please try again later.');
        }

        $therapistProfileArr = [
            'user_id' => $userObj->id,
            'address' => ($inputArr['address']) ? ($inputArr['address']) : (null),
            'latitude' => ($inputArr['latitude']) ? ($inputArr['latitude']) : (null),
            'longitude' => ($inputArr['longitude']) ? ($inputArr['longitude']) : (null),
            'experience' => ($inputArr['experience']) ? ($inputArr['experience']) : (null),
            'qualification' => ($inputArr['qualification']) ? ($inputArr['qualification']) : (null),
        ];
        $therapistProfileObj =  $therapistProfile->updateTherapistProfile($therapistProfileArr);

        $languagesArr = $request->get('languages');
        UserLanguage::where('user_id', $userObj->id)->delete();
        foreach ($languagesArr as $key => $languageId) {
            $userLanguageArr = [
                'user_id' => $userObj->id,
                'language_id' => $languageId
            ];
            $userlanguage->saveNewUserLanguages($userLanguageArr);
        }
        
        $specialismsArr = $request->get('specialisms');
        UserTherapistType::where('user_id', $userObj->id)->delete();
        foreach ($specialismsArr as $key => $specialismId) {
            $userSpecialismArr = [
                'user_id' => $userObj->id,
                'therapist_type_id' => $specialismId
            ];
            $userTherapistType->saveNewUserTherapistTypes($userSpecialismArr);
        }
        return redirect()->route('admin.therapist.index')->with('success_message', 'Therapist updated successfully.');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, User $user)
    {
        $userObj = $user->getUserById($id);
        if(!$userObj){
            return returnNotFoundResponse('This therapist does not exist');
        }

        $hasDeleted = $userObj->delete();
        if($hasDeleted){
            $userObj->therapistProfile()->delete();
            return returnSuccessResponse('Therapist deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
