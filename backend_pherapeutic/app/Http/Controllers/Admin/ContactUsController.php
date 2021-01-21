<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contactus;


class ContactUsController extends Controller
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
    public function index(Request $request, Contactus $contactus)
    {
        if ($request->ajax()) {
            $contactColl = $contactus->getAllContact();
            
            return datatables()->of($contactColl)
                ->addIndexColumn()
                ->addColumn('id', function ($contactus) {
                    return ($contactus->id) ? ($contactus->id) : 'N/A';
                })
                ->addColumn('name', function ($contactus) {
                    return ($contactus->name) ? ($contactus->name) : 'N/A';
                })
                ->addColumn('email', function ($contactus) {
                    return ($contactus->email) ? ($contactus->email) : 'N/A';
                })
                ->addColumn('subject', function ($contactus) {
                    return ($contactus->subject) ? ($contactus->subject) : 'N/A';
                })
                ->addColumn('action', function ($contactus) {
                    $btn = '';
                    $btn = '<a href="contactus/'.$contactus->id.'" title="View"><i class="fas fa-eye mr-1"></i></a>';
                    $btn .='<a href="javascript:void(0);" data-id="'.$contactus->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.contactus.index');
    }

 
    public function show($id)
    {
        $contactus = Contactus::find($id);

        return view('admin.contactus.show',compact('contactus'));
    }


    
    public function destroy($id, Contactus $contactus)
    {
        $contactus = $contactus->getContactById($id);

   

        $hasDeleted = $contactus->delete();
        if($hasDeleted){
            return returnSuccessResponse('Contact deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
