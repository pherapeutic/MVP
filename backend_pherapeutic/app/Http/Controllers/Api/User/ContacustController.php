<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Models\Contactus;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactUsMail;
use App\Http\Resources\contactus as contactusResource;

class ContacustController extends BaseController
{
    public function saveContact(Request $request) {    
        
        $input = $request->all();
       // dd($input);
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'message' => 'required',
      
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
          
       
    
//dd($input);
        $contactus = Contactus::create($input);
        $input2['name']= $input['name'];
        $input2['email']= $input['email'];
        $input2['subject']= $input['subject'];
        $input2['msg']= $input['message'];
        //dd($input2);
        $data= array(
             'message' =>  $input['message'],
             'subject'=> $input['subject'],
             'from'=>$input['email']
             );
        try{
            Mail::to('hello@pherapeutic.com')->send(new ContactUsMail($data));
        } catch(\Exception $ex){
            \Log::error($ex);
        }

        return $this->sendResponse(new contactusResource($input2), 'Thank you!');

    }
}

