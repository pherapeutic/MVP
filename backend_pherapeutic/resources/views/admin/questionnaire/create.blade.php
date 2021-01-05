@extends('layouts.admin')

@section('title') Create new questionnaire @endsection

@section('content')
<div class="row">
   <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Add New Question</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('admin.questionnaire.store')}}">
            <div class="card-body">
               @csrf
               <div class="row justify-content-center">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Question</label>
                        <input type="text" name="title" id="title" class="form-control form-control-solid" value="{{old('title')}}" placeholder="Enter your question" required/>
                        @if ($errors->has("title"))
                           <span class="form-text text-danger">
                              {{ $errors->first("title") }}
                           </span>
                        @endif
                     </div>
<!--                   </div> -->

                    <div class="new-fields-html d-none">
                        <div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="text" name="more[answer][]" class="form-control " value="{{old('answer')}}" placeholder="Enter answer">
                                </div>
                                <div class="col-md-2">
                                    <input id="point" type="number" class="form-control" name="more[point][]" value="{{ old('point') }}" placeholder="Points">
                                </div>
                                <div class="col-md-4">
                                    <a href="javascript:void(0);" class="add_new_row btn btn-primary">Add More</a>
                                    <a href="javascript:void(0);" class="remove_new_row d-none btn btn-danger">Remove</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="new_fields">
                        <div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="text" name="more[answer][]" class="form-control" value="{{old('answer')}}" placeholder="Enter answer">
                                </div>
                                <div class="col-md-3">
                                    <input id="point" type="number" min="1" class="form-control point" name="more[point][]" value="{{ old('point') }}" placeholder="Points">
                                </div>
                                <div class="col-md-3">
                                    <a href="javascript:void(0);" class="add_new_row btn btn-primary">Add More</a>
                                    <a href="javascript:void(0);" class="remove_new_row d-none btn btn-danger">Remove</a>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
               </div>
              
               <!-- end profile -->
            </div>

            <div class="card-footer">
              <div class="col-sm-12 text-center">              
                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                <button type="reset" class="btn btn-secondary">Cancel</button>
              </div>
            </div>
         </form>
         <!--end::Form-->
      </div>
   </div>
</div>

@endsection

@push('page_script')
   <script>
    $(document).ready(function () {

       addField();
       removeField();
   });

      function addField(){
          $(document).on('click', '.add_new_row', function(e){
              let isValid = true;
              if(!$('#title').val()){
                  alert('Please enter the question first.');
                  isValid = false;
                  return false;                  
              }

              if($(this).closest('.row').find(".point").val() <1){
                  alert('Please enter the valid point first.');
                  isValid = false;
                  return false;                 
              }

              $(this).closest('.row').find(":input").each(function() {
                  if(!$(this).val()){
                      alert('Please enter all the fields of this row');
                      isValid = false;
                      return false;
                  }
              });

              if(isValid){

                  $(this).closest('.row').find(":input").each(function() {
                      $(this).addClass('disabled-input');
                  });

                  $(this).addClass('d-none');
                  $(this).next().removeClass('d-none');
                  $(".new_fields").prepend($('.new-fields-html').html());
              } 
          });
      }

      function removeField(){
          $(document).on('click', '.remove_new_row', function(e){
              var removeVal = $(this).closest('.row').find(".item").val();
              $(this).closest('.row').remove();
          });
      }      
   </script>
@endpush
