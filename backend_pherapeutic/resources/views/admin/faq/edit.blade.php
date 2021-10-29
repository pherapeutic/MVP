@extends('layouts.admin')

@section('title') Edit FAQ @endsection

@section('content')
<div class="row">
   <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Edit FAQ</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('admin.faq.update',$faq->id)}}">
            <div class="card-body">
               @csrf
               @method('put')
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Question</label>
                        <input type="text" name="questions" class="form-control form-control-solid" placeholder="Enter title" value="{{ (old('questions')) ? (old('questions')) : ($faq->questions) }}" />
                        @if ($errors->has('questions'))
                              <span class="form-text text-danger">
                                 {{ $errors->first('questions') }}
                              </span>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Answer</label>
                        <input type="text" name="answers" class="form-control form-control-solid" placeholder="Enter description" value="{{ (old('answers')) ? (old('answers')) : ($faq->answers) }}" />
                        @if ($errors->has('answers'))
                              <span class="form-text text-danger">
                                 {{ $errors->first('answers') }}
                              </span>
                        @endif
                     </div>
                  </div>

                   <div class="col-md-6">
                     <div class="form-group">
                     <label>User Type</label>

                     <select name ="type_id" class="form-control" required>
                        <option value="">Select User Type</option>
                        <option value="0" {{$faq->type_id == 0?"selected":""}}>Client</option>
                        <option value="1" {{$faq->type_id == 1?"selected":""}}>Therapist</option>
                     </select>
                     </div>
               </div>
               </div>
          

         

            </div>
            <div class="card-footer">
               <button type="submit" class="btn btn-primary mr-2">Update</button>
               <!-- <button type="reset" class="btn btn-secondary">Cancel</button> -->
            </div>
         </form>
         <!--end::Form-->
      </div>
   </div>
</div>

@endsection
