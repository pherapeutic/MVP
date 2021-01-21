@extends('layouts.admin')

@section('title') Contact Details @endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4> Contact Details</h4>
            </div>
            <div class="card-body">

                <div class="row">

                   
                    <div class="col-6">
                      
                        <div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>Name: </th>
                                        <td class="p-2">{{$contactus->name ? $contactus->name : 'N/A'}}</td>
                                    </tr>
                                    <tr>
                                        <th>Email Id: </th>
                                        <td class="p-2">{{$contactus->email ? $contactus->email : 'N/A'}}</td>
                                    </tr>
                                    <tr>
                                        <th>Subject: </th>
                                        <td class="p-2">
                                            {{$contactus->subject ? ($contactus->subject) : 'N/A'}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Message: </th>
                                        <td class="p-2">
                                        {{$contactus->message ? ($contactus->message) : 'N/A'}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                  
                    <div class="col-12 text-center mt-5">
                        <a class="btn btn-dark" href="{{URL::previous()}}" style="color:white;">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection