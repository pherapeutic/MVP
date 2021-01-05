{{-- Extends layout --}}
@extends('layouts.admin.app')

{{-- Content --}}
@section('content')

<div class="container">
   <div class="row">
    <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Edit therapist</h3>
            <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div>
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('therapist.update',$user->id)}}">
            <div class="card-body">
              @csrf
              @method('put')
               <div class="form-group">
                <input type="hidden" value="{{ $user->id }}" name="user_id"/>
                <!-- <input type="hidden" value="Therapist" name="user[role]"/> -->
                  <label>First Name:</label>
                  <input type="text" name="user[first_name]" class="form-control form-control-solid" value="{{ old('first_name') ? old('first_name') : $user->first_name }}" placeholder="Enter first name" />
                  @if ($errors->has("user[first_name]"))
                      <span class="form-text text-muted">
                        {{ $errors->first("user[first_name]") }}
                      </span>
                  @endif
               </div>
               <div class="form-group">
                  <label>Last Name:</label>
                  <input type="text" name="user[last_name]" class="form-control form-control-solid" value="{{ old('last_name') ? old('last_name') : $user->last_name }}" placeholder="Enter last name" />
                  @if ($errors->has("user[last_name]"))
                        <span class="form-text text-muted">
                          {{ $errors->first("user[last_name]") }}
                        </span>
                    @endif
               </div>
               <div class="form-group">
                  <label>Email address:</label>
                  <input type="email" name="user[email]" class="form-control form-control-solid" value="{{ old('email') ? old('email') : $user->email }}" placeholder="Enter email" />
                  @if ($errors->has("user[email]"))
                      <span class="form-text text-muted">
                        {{ $errors->first("user[email]") }}
                      </span>
                    @endif
               </div>
               <!-- start profile -->
               @if($profile)
               <div class="form-group">
                  <label>Address:</label>
                  <input type="text" name="profile[address]" class="form-control form-control-solid" value="{{ old('address') ? old('address') : $profile->address }}" placeholder="Enter last name" />
                  @if ($errors->has("profile[address]"))
                        <span class="form-text text-muted">
                          {{ $errors->first("profile[address]") }}
                        </span>
                    @endif
               </div>
               <div class="form-group">
                  <label>Experience:</label>
                  <input type="text" name="profile[experience]" class="form-control form-control-solid" value="{{ old('experience') ? old('experience') : $profile->experience }}" placeholder="Enter last name" />
                  @if ($errors->has("profile[experience]"))
                        <span class="form-text text-muted">
                          {{ $errors->first("profile[experience]") }}
                        </span>
                    @endif
               </div>
               <div class="form-group">
                  <label>Qaulification:</label>
                  <input type="text" name="profile[qaulification]" class="form-control form-control-solid" value="{{ old('qaulification') ? old('qaulification') : $profile->qaulification }}" placeholder="Enter last name" />
                  @if ($errors->has("profile[qaulification]"))
                        <span class="form-text text-muted">
                          {{ $errors->first("profile[qaulification]") }}
                        </span>
                    @endif
               </div>
               @else
                <div class="form-group">
                  <label>Address:</label>
                  <input type="text" name="profile[address]" class="form-control form-control-solid" value="{{old('profile[address]')}}" placeholder="Enter last name" />
                  @if ($errors->has("profile[address]"))
                        <span class="form-text text-muted">
                          {{ $errors->first("profile[address]") }}
                        </span>
                    @endif
               </div>
               <div class="form-group">
                  <label>Experience:</label>
                  <input type="text" name="profile[experience]" class="form-control form-control-solid" value="{{old('profile[experience]')}}" placeholder="Enter last name" />
                  @if ($errors->has("profile[experience]"))
                        <span class="form-text text-muted">
                          {{ $errors->first("profile[experience]") }}
                        </span>
                    @endif
               </div>
               <div class="form-group">
                  <label>Qaulification:</label>
                  <input type="text" name="profile[qaulification]" class="form-control form-control-solid" value="{{old('profile[qaulification]')}}" placeholder="Enter last name" />
                  @if ($errors->has("profile[qaulification]"))
                        <span class="form-text text-muted">
                          {{ $errors->first("profile[qaulification]") }}
                        </span>
                    @endif
               </div>
               @endif
               <!-- end profile -->
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
               <button type="reset" class="btn btn-secondary">Cancel</button>
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
