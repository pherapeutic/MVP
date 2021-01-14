<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Appointments;
use App\Models\User;
use App\Models\Rating;
use App\Models\PaymentDetails;
use Carbon\Carbon;
class PaymentsController extends Controller
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
    public function index(Request $request, PaymentDetails $paymentDetails, User $user)
    {
        
        if ($request->ajax()) {
            $appointmentsColl = $paymentDetails->getAllPayments();
            return datatables()->of($appointmentsColl)
                ->addIndexColumn()
                ->addColumn('id', function ($paymentDetails) {
                    return ($paymentDetails->id) ? ($paymentDetails->id) : 'N/A';
                })
                ->addColumn('user_name', function ($paymentDetails) use ($user) {

                    $name = $user->getUserNameId($paymentDetails->callLogs->user_id);
                    
                    return ($name) ? ($name) : 'N/A';
                })
                ->addColumn('therapist_name', function ($paymentDetails) use ($user) {
                    $name = $user->getUserNameId($paymentDetails->callLogs->therapist_id);
                    return ($name) ? ($name) : 'N/A';
                })
                ->addColumn('created_at', function ($paymentDetails) {
                    return ($paymentDetails->created_at) ? (\Carbon\Carbon::parse($paymentDetails->created_at)->format('d M Y H:m A')) : 'N/A';
                })
                ->addColumn('amount', function ($paymentDetails) {
                    return ($paymentDetails->amount) ? ('£ '.$paymentDetails->amount) : 'N/A';
                })                
                ->addColumn('status', function ($paymentDetails) {
                    $status = 'N/A';
                    if($paymentDetails->is_captured == '0'){
                        $status = '<span class="badge badge-warning">Hold Payment</span>';
                    }else if($paymentDetails->is_captured == '1'){
                        $status = '<span class="badge badge-success">Payment Done</span>';
                    }else if($paymentDetails->is_captured == '2'){
                        $status = '<span class="badge badge-info">Refund</span>';
                    }else if($paymentDetails->is_captured == '3'){
                        $status = '<span class="badge badge-success">Payment And Refund</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($paymentDetails) {
                    $btn = '';
                    $btn = '<a href="payments/'.$paymentDetails->id.'" title="View"><i class="fas fa-eye mr-1"></i></a>';
                   // $btn = '<a href="payments/'.$paymentDetails->id.'/edit" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                   //  $btn .='<a href="javascript:void(0);" data-id="'.$paymentDetails->id.'" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                    
                    return $btn;
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }
        return view('admin.payments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.payments.create');
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

        return redirect()->route('payments.index')->with('success_message', 'Appointments account created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response/
     */
    public function show($id)
    {
        $paymentObj = PaymentDetails::find($id);

        if(!$paymentObj){
            return redirect()->route('admin.payments.index')->with('error_message', 'Appointment not found.');            
        }
        $callLogObj = $paymentObj->callLogs;

        return view('admin.payments.show',compact('callLogObj', 'paymentObj'));
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

        return view('admin.payments.edit', compact('appointments'));
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
            return redirect()->route('payments.index')->with('success_message', 'appointments updated successfully.');
        }
        return redirect()->back()->with('error_message', 'Unable to update payments. Please try again later.');
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
