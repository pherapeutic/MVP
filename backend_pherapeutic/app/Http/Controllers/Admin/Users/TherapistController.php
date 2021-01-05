<?php

namespace App\Http\Controllers\Admin\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTherapistRequest;
use App\Http\Requests\UpdateTherapistRequest;
use App\Models\User;
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
        $page_title = 'Datatables';
        $page_description = 'This is datatables test page';
        if ($request->ajax()) {
            $usersColl = $user->getAllTherapists();
            //echo "<pre>"; print_r($usersColl); die;
            return datatables()->of($usersColl)
                ->addIndexColumn()
                ->addColumn('id', function ($user) {
                   return $user->id;
                })
                ->addColumn('first_name', function ($user) {
                    return ($user->first_name) ? ($user->first_name) : 'N/A';
                })
                ->addColumn('last_name', function ($user) {
                    return ($user->last_name) ? ($user->last_name) : 'N/A';
                })
                ->addColumn('email', function ($user) {
                    return ($user->email) ? ($user->email) : 'N/A';
                })
                ->addColumn('action', function ($user) {
                    $btn = '';
                    $btn = '<a href="therapist/'.$user->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$user->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.users.therapist.index', compact('page_title', 'page_description'));
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
    public function store(CreateTherapistRequest $request, User $user)
    {
        //echo"<pre>";print_r($request->all());die;
        
        $inputArr = $request->except(['_token', 'confirm_password']);

        $userarr['role'] = $inputArr['role'];
        $userarr['first_name'] = $inputArr['first_name'];
        $userarr['last_name'] = $inputArr['last_name'];
        $userarr['email'] = $inputArr['email'];
        $userarr['password'] = $inputArr['password'];

        $profilearr['address'] = $inputArr['address'];
        $profilearr['experience'] = $inputArr['experience'];
        $profilearr['qaulification'] = $inputArr['qaulification'];
        $profilearr['specialism'] = $inputArr['specialism'];
      

        $userObj = $user->saveNewUser($userarr);
        //$usr = $user->create($inputArr['user']);
        $profilearr['latitude']   = '';
        $profilearr['longitude'] = '';
        $profilearr['user_id']    = $userObj->id;
        //profile
        $profile = new TherapistProfile();
        $profile->create($profilearr);
        //$user->therapistprofile()->create($inputArr['profile']);
        //echo"<pre>";print_r($request->all());die;
        if(!$userObj){
            return redirect()->back()->with('error', 'Unable to add therapist. Please try again later.');
        }

        return redirect()->route('therapist.index')->with('success', 'Therapist account added successfully.');
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

        $user = $user->getUserById($id);
        $profile = TherapistProfile::where('user_id','=',$id)->first();
        //echo"<pre>";print_r($user);die;
        if(!$user){
            return redirect()->back()->with('error', 'therapist does not exist');
        }

        return view('admin.users.therapist.edit', compact('user','profile'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTherapistRequest $request, $id)
    {
       //echo"<pre>";print_r($request->all());die;
       $inputArr = $request->except(['_token', 'confirm_password']);

        //$userarr['role'] = $inputArr['role'];
        $userarr['first_name'] = $inputArr['first_name'];
        $userarr['last_name'] = $inputArr['last_name'];
        $userarr['email'] = $inputArr['email'];
        //$userarr['password'] = $inputArr['password'];

        $profilearr['address'] = $inputArr['address'];
        $profilearr['experience'] = $inputArr['experience'];
        $profilearr['qaulification'] = $inputArr['qaulification'];
        $profilearr['specialism'] = $inputArr['specialism'];
        $user = new User();
        $user = $user->getUserById($id);
        if(!$user){
            return redirect()->back()->with('error', 'This therapist does not exist');
        }

        $inputArr = $request->except(['_token', 'user_id', '_method']);
        $hasUpdated = $user->updateUser($id, $userarr);

        $profile =TherapistProfile::where('user_id','=',$id)->first();
        //echo"<pre>";print_r($inputArr['profile']);die;
        $profile->update($profilearr);

        if($hasUpdated){
            return redirect()->route('therapist.index')->with('success', 'Therapist updated successfully.');
        }
        return redirect()->back()->with('error', 'Unable to update therapist. Please try again later.');
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
            return returnSuccessResponse('therapist deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
