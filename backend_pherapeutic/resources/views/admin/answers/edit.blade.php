{{-- Extends layout --}}
@extends('layouts.admin.app')

{{-- Content --}}
@section('content')
<div class="container">
   <div class="row">
    <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Edit Answer</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('answers.update',$answers->id)}}">
            <div class="card-body">
              <div class="form-group">
                @csrf
                @method('put')
                <input type="hidden" value="{{ $answers->id }}" name="answer_id"/>
                <label>Title:</label>
                  <input type="text" name="title" class="form-control form-control-solid" value="{{$answers->title}}" placeholder="Enter Title" required/>
                  @if ($errors->has('title'))
                        <span class="form-text text-muted">
                            {{ $errors->first('title') }}
                        </span>
                    @endif
               </div>
               <div class="form-group">
                <label for="question_id">Select Question</label>
                <select class="form-control" id="question_id" name="question_id" required>
                  <option value="">select</option>
                  <?php
                    foreach ($questions as $question) {
                      if($question == $answers->question_id){
                          $selected ='selected';
                      }
                      else{
                          $selected ='';
                      }
                      echo "<option value='$question->id' $selected>$question->title</option>";
                    }
                  ?>
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
