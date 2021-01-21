<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Settings;
use Validator;

class SettingController extends Controller
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
    public function index()
    {
        $settingObj = Settings::first();
        if(!$settingObj){
            $id = 0;
            return view('admin.settings.create', compact('id'));            
        }
        return view('admin.settings.edit', compact('settingObj'));         
    }

    public function store(Request $request){
        $rules = [
            'app_charge' => 'required',
        ];

        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        Settings::create($input);

        return redirect()->route('admin.settings.index')->with('success_message', 'Change setting successfully.');        
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'app_charge' => 'required',
        ];

        $input = $request->all();
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $settingObj = Settings::where('id', $id)->first();
        if(!$settingObj){
            return redirect()->back()->with('error_message', 'Setting does not exist');
        }
        $settingObj->app_charge = $input['app_charge'];
        $settingObj->save();
        return redirect()->route('admin.settings.index')->with('success_message', 'Change setting successfully.');        
    }    
}
