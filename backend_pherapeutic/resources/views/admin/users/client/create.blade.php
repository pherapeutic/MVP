@extends('layouts.admin')

@section('title') Create new client @endsection

@section('content')
<div class="row">
   <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Add New Client</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('admin.client.store')}}">
            @csrf
            <div class="card-body">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control form-control-solid" placeholder="Enter first name" value="{{old('first_name')}}" required/>
                        @if ($errors->has('first_name'))
                           <span class="form-text text-danger">{{ $errors->first('first_name') }}</span>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="{{old('last_name')}}"  class="form-control form-control-solid" placeholder="Enter last name" required/>
                        @if ($errors->has('last_name'))
                              <span class="form-text text-danger">{{ $errors->first('last_name') }}</span>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <label>Email address</label>
                  <input type="email" name="email" class="form-control form-control-solid" placeholder="Enter email" value="{{old('email')}}"  required/>
                  @if ($errors->has('email'))
                        <span class="form-text text-danger">{{ $errors->first('email') }}</span>
                    @endif
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control form-control-solid" placeholder="Password" required/>
                        @if ($errors->has('password'))
                              <span class="form-text text-danger">{{ $errors->first('password') }}</span>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control form-control-solid" placeholder="Confirm Password" required/>
                        @if ($errors->has('confirm_password'))
                              <span class="form-text text-danger">{{ $errors->first('confirm_password') }}</span>
                        @endif
                     </div>
                  </div>

                  <div class="col-md-12">
                     <div class="form-group">
                        <label>Select languages which client can speak</label>
                        <select class="form-control select2" id="languages" name="languages[]" multiple="multiple" required>
                           @foreach(getLanguages() as $languageId => $language)
                              <option value="{{ $languageId }}" {{ (old('languages') && in_array($languageId, old('languages'))) ? ('selected="selected"') : ('') }}>{{ $language }}</option>
                           @endforeach
                        </select>
                        @if ($errors->has('languages'))
                              <span class="form-text text-danger">{{ $errors->first('languages') }}</span>
                        @endif
                     </div>
                  </div>
               </div>
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

@endsection

@push('page_script')
   <script src="{{ asset('assets/js/pages/crud/forms/widgets/select2.js') }}"></script>
   <script>
      $('#languages').select2({
         placeholder: "Select languages",
      });
   </script>
@endpush
