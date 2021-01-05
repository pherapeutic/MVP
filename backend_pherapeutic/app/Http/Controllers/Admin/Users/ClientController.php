<?php

namespace App\Http\Controllers\Admin\Users;

use Illuminate\Http\Request;
use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
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
            $usersColl = $user->getAllClients();
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
                    $btn = '<a href="client/'.$user->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$user->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
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
    public function store(CreateClientRequest $request, User $user)
    {
        $inputArr = $request->except(['_token', 'confirm_password']);
        $userObj = $user->saveNewUser($inputArr);
        if(!$userObj){
            return redirect()->back()->with('error', 'Unable to add Customer. Please try again later.');
        }

        return redirect()->route('client.index')->with('success', 'Customer account added successfully.');
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
        if(!$user){
            return redirect()->back()->with('error', 'Customer does not exist');
        }

        return view('admin.users.client.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientRequest $request, $id)
    {
        //echo"<pre>";print_r($request->all());die;
        $user = new User();
        $user = $user->getUserById($id);
        if(!$user){
            return redirect()->back()->with('error', 'This Customer does not exist');
        }

        $inputArr = $request->except(['_token', 'user_id', '_method']);
        $hasUpdated = $user->updateUser($id, $inputArr);

        if($hasUpdated){
            return redirect()->route('client.index')->with('success_message', 'Customer updated successfully.');
        }
        return redirect()->back()->with('error', 'Unable to update customer. Please try again later.');
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
            return returnNotFoundResponse('This customer does not exist');
        }

        $hasDeleted = $userObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Customer deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
