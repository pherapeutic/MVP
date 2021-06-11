@extends('layouts.admin')

@section('title') Create Specialism @endsection

@section('content')
<div class="container">
   <div class="row">
    <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Add New Specialism</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('admin.therapisttypes.store')}}">
            <div class="card-body">
            @csrf

            <div class="row">

               <div class="form-group col-md-6">
                  <label>Title:</label>
                  <input type="text" name="title" class="form-control form-control-solid {{ $errors->has('title') ? ' is-invalid' : '' }}" placeholder="Enter Title" value="{{ old('title') }}"/>
                  @if ($errors->has('title'))
                        <span class="form-text text-muted">
                            {{ $errors->first('title') }}
                        </span>
                    @endif
               </div>
              </div>

              <div class="row">

               <div class="form-group col-md-6">
                  <label>Min Point:</label>
                  <input type="text" name="min_point" class="form-control form-control-solid" placeholder="Enter Min Point" value="{{ old('min_point') }}" />
                  @if ($errors->has('min_point'))
                        <span class="form-text text-muted">
                            {{ $errors->first('min_point') }}
                        </span>
                    @endif
               </div>
              </div>

              <div class="row">

               <div class="form-group col-md-6">
                  <label>Max Point:</label>
                  <input type="text" name="max_point" class="form-control form-control-solid" placeholder="Enter Max Point" value="{{ old('max_point') }}"/>
                  @if ($errors->has('max_point'))
                        <span class="form-text text-muted">
                            {{ $errors->first('max_point') }}
                        </span>
                    @endif
               </div>
              </div>
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
