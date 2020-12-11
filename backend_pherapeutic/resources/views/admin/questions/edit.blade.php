{{-- Extends layout --}}
@extends('layouts.admin.app')

{{-- Content --}}
@section('content')
<div class="container">
   <div class="row">
    <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Edit Question</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('questions.update',$questions->id)}}">
            <div class="card-body">
              <div class="form-group">
                @csrf
                @method('put')
                <input type="hidden" value="{{ $questions->id }}" name="question_id"/>
                <label>Title:</label>
                  <input type="text" name="title" class="form-control form-control-solid" value="{{$questions->title}}" placeholder="Enter Title" required/>
                  @if ($errors->has('title'))
                        <span class="form-text text-muted">
                            {{ $errors->first('title') }}
                        </span>
                    @endif
               </div>
               <div class="form-group">
                <label for="status">Status</label>
                <?php
                  $active ='';
                  $inactive ='';
                  if($questions->status =='active'){
                    $active ="selected";
                  }else{
                    $inactive ="selected";
                  }
                ?>
                <select class="form-control" id="status" name="status" required>
                  <option value="">select</option>
                  <option value="active" {{$active}} >Active</option>
                  <option value="in-active" {{$inactive}} >In-active</option>
                </select>
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
</div>

@endsection

{{-- Styles Section --}}
@section('styles')
    
@endsection


{{-- Scripts Section --}}
@section('scripts')
   
@endsection
