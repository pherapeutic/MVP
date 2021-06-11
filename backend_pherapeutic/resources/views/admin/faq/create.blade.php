@extends('layouts.admin')

@section('title') Create new FAQ @endsection

@section('content')
<div class="row">
   <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Add New FAQ</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('admin.faq.store')}}">
            @csrf
            <div class="card-body">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Question</label>
                        <input type="text" name="questions" class="form-control form-control-solid" placeholder="Enter question" value="{{old('questions')}}" required/>
                        @if ($errors->has('title'))
                           <span class="form-text text-danger">{{ $errors->first('questions') }}</span>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Answer</label>
                        <input type="text" name="answers" value="{{old('answers')}}"  class="form-control form-control-solid" placeholder="Enter answer" required/>
                        @if ($errors->has('answers'))
                              <span class="form-text text-danger">{{ $errors->first('answers') }}</span>
                        @endif
                     </div>
                  </div>
               </div>

               <div class="col-md-6">
                     <div class="form-group">
                     <label>User Type</label>

                     <select name ="type_id" class="form-control" required>
                        <option value="">Select User Type</option>
                        <option value="0">Client</option>
                        <option value="1">Therapist</option>
                     </select>
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


