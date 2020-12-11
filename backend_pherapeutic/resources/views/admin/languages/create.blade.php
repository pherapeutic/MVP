{{-- Extends layout --}}
@extends('layouts.admin.app')
{{-- Content --}}
@section('content')
<div class="container">
   <div class="row">
    <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Add Language</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('languages.store')}}">
            <div class="card-body">
               <div class="form-group">
                @csrf
                  <label>Title:</label>
                  <input type="text" name="title" class="form-control form-control-solid" placeholder="Enter Title" required/>
                  @if ($errors->has('title'))
                        <span class="form-text text-muted">
                            {{ $errors->first('title') }}
                        </span>
                    @endif
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
