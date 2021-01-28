<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Appointments;
use App\Models\Contactus;
use App\Models\PaymentDetails;


class HomeController extends Controller
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
        $totalClient = User::where('role', '=', User::CLIENT_ROLE)
                        ->where('email_verified_at', '!=', 'null')->count();
        $totalTherapist = User::where('role', '=', User::THERAPIST_ROLE)
                            ->where('email_verified_at', '!=', 'null')->count();

        $totalAppointments = Appointments::count();
        $contactUs = Contactus::count();
        $monthlys = User::monthly();
        $monthlyPayments = json_encode($monthlys['payments']);
        $users = json_encode($monthlys['users']);
        $therapists = json_encode($monthlys['therapists']);
        $categories = json_encode($monthlys['month']);
        return view('admin.home', compact('totalClient','totalTherapist','therapists','users','categories','totalAppointments','contactUs','monthlyPayments'));
    }
}
