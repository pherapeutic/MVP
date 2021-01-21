<?php

namespace App\Http\Controllers\Admin\Users;

use Illuminate\Http\Request;
use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLanguage;

class ClientController extends Controller
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
            $userColl = $user->getAllClients();
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
                ->addColumn('action', function ($userObj) {
                    $btn = '';
                    $btn = '<a href="client/'.$userObj->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$userObj->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.users.client.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.client.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateClientRequest $request, User $user, UserLanguage $userlanguage)
    {
        $languagesArr = $request->get('languages');
        $inputArr = $request->except(['_token', 'confirm_password', 'languages']);
        $inputArr['role'] = '0';
        
        $userObj = $user->saveNewUser($inputArr);
        if(!$userObj){
            return redirect()->back()->with('error_message', 'Unable to create new client. Please try again later.');
        }

        foreach ($languagesArr as $key => $languageId) {
            $userLanguageArr = [
                'user_id' => $userObj->id,
                'language_id' => $languageId
            ];
            $userlanguage->saveNewUserLanguages($userLanguageArr);
        }

        return redirect()->route('admin.client.index')->with('success_message', 'New client created successfully.');
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
            return redirect()->back()->with('error_message', 'Customer does not exist');
        }

        return view('admin.users.client.edit', compact('userObj'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, UpdateClientRequest $request, User $user, Userlanguage $userlanguage)
    {
        $userObj = $user->getUserById($id);
        if(!$userObj){
            return redirect()->back()->with('error_message', 'This Customer does not exist');
        }

        $languagesArr = $request->get('languages');
        $inputArr = $request->except(['_token', '_method', 'languages']);
        $hasUpdated = $userObj->updateUser($id, $inputArr);
        
        if(!$hasUpdated){
            return redirect()->back()->with('error_message', 'Unable to update client. Please try again later.');
        }

        UserLanguage::where('user_id', $userObj->id)->delete();
        foreach ($languagesArr as $key => $languageId) {
            $userLanguageArr = [
                'user_id' => $userObj->id,
                'language_id' => $languageId
            ];
            $userlanguage->saveNewUserLanguages($userLanguageArr);
        }
        return redirect()->route('admin.client.index')->with('success_message', 'Client detail updated successfully.');
        
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
            return returnNotFoundResponse('This client does not exist');
        }

        $hasDeleted = $userObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Client deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }

}
