@extends('layouts.admin')

@section('title') Create new Terms & Conditions @endsection

@section('content')
<div class="row">
   <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Add New Terms & Conditions</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('admin.termsandconditions.store')}}">
            @csrf
            <div class="card-body">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control form-control-solid" placeholder="Enter title" value="{{old('title')}}" required/>
                        @if ($errors->has('title'))
                           <span class="form-text text-danger">{{ $errors->first('title') }}</span>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" value="{{old('description')}}"  class="form-control form-control-solid" placeholder="Enter description" required/>
                        @if ($errors->has('description'))
                              <span class="form-text text-danger">{{ $errors->first('description') }}</span>
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


