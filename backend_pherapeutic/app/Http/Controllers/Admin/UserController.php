<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    public function edit($id){

    	$model = User::find($id);
        if(!$model){
            return redirect()->back()->with('error_message', 'User does not exist');
        }

        return view('admin.users.admin.edit', compact('model'));
    
    }

     public function update($id,Request $request)

    {
    	$rules = array(
            'first_name'=>'required',
            'email' => 'required|email|unique:users,email,'.$id.',id,deleted_at,NULL',
             'contact_no' => 'required|unique:users,contact_no,'.$id.',id,deleted_at,NULL'
        );

    	$validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $model = Auth()->user();
        $model->first_name = $request->first_name;
        $model->email = $request->email;
        $model->contact_no = $request->contact_no;
        if($request->hasFile('image')){
        $model->saveUploadedFile($request,'image');
        }
        if($model->save()){

                return redirect('/admin/user/client')->with('success_message', 'Profile updated successfully');
  
        }

        return redirect()->back()->with('error_message', 'Unable to update user. Please try again later.');


    }

     public function changePasswordView()
    {
        return view('admin.users.admin.change-password');
    }

    public function changePassword(Request $request)
    {
        $rules = array(
            'password' => 'required|min:8',
            'confirm_password'=>'required|same:password'                        
        );
        $message = ['confirm_password.same'=>'Password and confirm password should be same.'];
        $validator = Validator::make($request->all(), $rules,$message);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }  

        $model = User::find(auth()->user()->id);

        if(!empty($model)){

            $model->password = $request->input('password');
        
            if($model->save()){


                return redirect('/admin/user/client')->with('success_message', 'Password updated successfully');

            }
        }

        return Redirect::back()->withInput()->with('error_message', 'Some error occured. Please try again later');


    }


}
