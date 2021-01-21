{{-- Extends layout --}}
@extends('layouts.admin.app')
{{-- Content --}}
@section('content')
<div class="container">
   <div class="row">
    <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Add Questionnaire</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('faqs.store')}}">
            <div class="card-body">
               <div class="form-group">
                @csrf
                  <label>Questions:</label>
                  <input type="text" name="question" class="form-control form-control-solid" placeholder="Enter Question" />
                  @if ($errors->has('question'))
                        <span class="form-text text-muted">
                            {{ $errors->first('question') }}
                        </span>
                    @endif
               </div>
               <div class="form-group">
                  <label>Answer:</label>
                  <input type="text" name="answer" class="form-control form-control-solid" placeholder="Enter Answer" />
                  @if ($errors->has('answer'))
                        <span class="form-text text-muted">
                            {{ $errors->first('answer') }}
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
