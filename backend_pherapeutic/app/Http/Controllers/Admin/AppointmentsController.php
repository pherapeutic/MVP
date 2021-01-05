<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Appointments;
use App\Models\User;
use App\Models\Rating;
use Carbon\Carbon;
class AppointmentsController extends Controller
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
    public function index(Request $request, Appointments $appointments, User $user)
    {
        if ($request->ajax()) {
            $appointmentsColl = $appointments->getAllAppointments();
            return datatables()->of($appointmentsColl)
                ->addIndexColumn()
                ->addColumn('id', function ($appointments) {
                    return ($appointments->id) ? ($appointments->id) : 'N/A';
                })
                ->addColumn('user_name', function ($appointments) use ($user) {
                    $name = $user->getUserNameId($appointments->user_id);
                    
                    return ($name) ? ($name) : 'N/A';
                })
                ->addColumn('therapist_name', function ($appointments) use ($user) {
                    $name = $user->getUserNameId($appointments->therapist_id);
                    return ($name) ? ($name) : 'N/A';
                })
                ->addColumn('status', function ($appointments) {
                    $status = 'N/A';
                    if($appointments->status == '0'){
                        $status = '<span class="badge badge-secondary">Waiting</span>';
                    }else if($appointments->status == '1'){
                        $status = '<span class="badge badge-info">Connected</span>';
                    }else if($appointments->status == '2'){
                        $status = '<span class="badge badge-success">Completed</span>';
                    }else if($appointments->status == '3'){
                        $status = '<span class="badge badge-warning">Payment Done</span>';
                    }else if($appointments->status == '4'){
                        $status = '<span class="badge badge-danger">Disconnected</span>';
                    }
                    return $status;
                })
                ->addColumn('is_trail', function ($appointments) {
                    if($appointments->is_trail ==1){
                        $return ="Yes";
                    }else{
                       $return = 'No';  
                    }
                    return ($return) ? ($return) : 'N/A';
                })
                ->addColumn('ended_at', function ($appointments) {
                    if($appointments->ended_at){
                        return Carbon::parse($appointments->ended_at)->format('d M Y');
                    }else{
                       return 'No';  
                    }
                })
                ->addColumn('action', function ($appointments) {
                    $btn = '';
                    $btn = '<a href="appointments/'.$appointments->id.'" title="View"><i class="fas fa-eye mr-1"></i></a>';
                   // $btn = '<a href="appointments/'.$appointments->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                   //  $btn .='<a href="javascript:void(0);" data-id="'.$appointments->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return view('admin.appointments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.appointments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Appointments $appointments)
    {
        //echo"<pre>";print_r($request->all());die;
        $inputArr = $request->except(['_token']);
        $appointmentsObj = $appointments->saveNewAppointment($inputArr);
        if(!$appointmentsObj){
            return redirect()->back()->with('error_message', 'Unable to create Question. Please try again later.');
        }

        return redirect()->route('appointments.index')->with('success_message', 'Appointments account created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $appointmentObj = Appointments::find($id);
        if(!$appointmentObj){
            return redirect()->route('admin.appointments.index')->with('error_message', 'Appointment not found.');            
        }
        return view('admin.appointments.show',compact('appointmentObj'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,  Appointments $appointments)
    {

        $appointments = $appointments->getQuestionById($id);
        if(!$appointments){
            return redirect()->back()->with('error_message', 'Appointments does not exist');
        }

        return view('admin.appointments.edit', compact('appointments'));
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
        $appointments = new Appointments();
        $appointments = $appointments->getQuestionById($id);
        if(!$appointments){
            return redirect()->back()->with('error_message', 'This appointment does not exist');
        }

        $inputArr = $request->except(['_token', 'appointment_id', '_method']);
        $hasUpdated = $appointments->updateQuestion($id, $inputArr);

        if($hasUpdated){
            return redirect()->route('appointments.index')->with('success_message', 'appointments updated successfully.');
        }
        return redirect()->back()->with('error_message', 'Unable to update appointments. Please try again later.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Appointments $appointments)
    {
        $appointmentsObj = $appointments->getAppointmentsById($id);

        if(!$appointmentsObj){
            return returnNotFoundResponse('This question does not exist');
        }

        $hasDeleted = $qappointmentsObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Question deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
