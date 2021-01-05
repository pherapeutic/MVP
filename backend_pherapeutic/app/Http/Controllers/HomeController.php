<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
        $clientlist = User::where('role', '=','Client')->get();
        $therapistlist = User::where('role', '=','Therapist')->get();
        $clientCount = $clientlist->count();
        $therapistCount = $therapistlist->count();
        return view('home', compact('clientCount','therapistCount'));
    }
}
