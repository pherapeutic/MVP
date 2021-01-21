@extends('layouts.admin')

@section('title') Edit Terms @endsection

@section('content')
<div class="row">
   <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Edit Terms & Conditions</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('admin.termsandconditions.update',$termsandconditionsObj->id)}}">
            <div class="card-body">
               @csrf
               @method('put')
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control form-control-solid" placeholder="Enter title" value="{{ (old('title')) ? (old('title')) : ($termsandconditionsObj->title) }}" />
                        @if ($errors->has('title'))
                              <span class="form-text text-danger">
                                 {{ $errors->first('title') }}
                              </span>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control form-control-solid" placeholder="Enter description" value="{{ (old('description')) ? (old('description')) : ($termsandconditionsObj->description) }}" />
                        @if ($errors->has('description'))
                              <span class="form-text text-danger">
                                 {{ $errors->first('description') }}
                              </span>
                        @endif
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

@push('page_script')
   <script src="{{ asset('assets/js/pages/crud/forms/widgets/select2.js') }}"></script>
   <script>
      $('#languages').select2({
         placeholder: "Select languages",
      });
   </script>
@endpush
