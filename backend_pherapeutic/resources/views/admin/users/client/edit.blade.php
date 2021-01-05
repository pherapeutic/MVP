{{-- Extends layout --}}
@extends('layouts.admin.app')

{{-- Content --}}
@section('content')
<div class="container">
   <div class="row">
    <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Edit Customer</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('client.update',$user->id)}}">
            <div class="card-body">
               <div class="form-group">
                @csrf
                @method('put')
                    <input type="hidden" value="{{ $user->id }}" name="user_id"/>
                    <input type="hidden" value="Client" name="role"/>
                  <label>First Name:</label>
                  <input type="text" name="first_name" class="form-control form-control-solid" placeholder="Enter first name" value="{{ $user->first_name }}" />
                  @if ($errors->has('first_name'))
                        <span class="form-text text-muted">
                            {{ $errors->first('first_name') }}
                        </span>
                    @endif
               </div>
               <div class="form-group">
                  <label>Last Name:</label>
                  <input type="text" name="last_name" class="form-control form-control-solid" placeholder="Enter last name" value="{{ $user->last_name }}" />
                  @if ($errors->has('last_name'))
                        <span class="form-text text-muted">
                            {{ $errors->first('last_name') }}
                        </span>
                    @endif
               </div>
               <div class="form-group">
                  <label>Email address:</label>
                  <input type="email" name="email" class="form-control form-control-solid" placeholder="Enter email"value="{{ $user->email }}" />
                  @if ($errors->has('email'))
                        <span class="form-text text-muted">
                            {{ $errors->first('email') }}
                        </span>
                    @endif
               </div>
              {{--
               <div class="form-group">
                  <label>Password:</label>
                  <input type="password" name="password" class="form-control form-control-solid" placeholder="Password" />
                    @if ($errors->has('password'))
                        <span class="form-text text-muted">
                            {{ $errors->first('password') }}
                        </span>
                    @endif
               </div>
               <div class="form-group">
                  <label>Confirm Password:</label>
                  <input type="password" name="confirm_password" class="form-control form-control-solid" placeholder="Confirm Password" />
                    @if ($errors->has('confirm_password'))
                        <span class="form-text text-muted">
                            {{ $errors->first('confirm_password') }}
                        </span>
                    @endif
                  
               </div>
            --}}
            </div>
            <div class="card-footer">
               <button type="submit" class="btn btn-primary mr-2">Submit</button>
               <!-- <button type="reset" class="btn btn-secondary">Cancel</button> -->
            </div>
         </form>
         <!--end::Form-->
      </div>
    </div>
   </div>
</div>

@endsection

{{-- Styles Section --}}
@section('styles')
    
@endsection


{{-- Scripts Section --}}
@section('scripts')
   
@endsection
