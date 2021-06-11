@extends('layouts.admin')

@section('title') Edit questionnaire @endsection

@section('content')

<div class="row">
  <div class="col-lg-12">
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
          <h3 class="card-title">Edit questionnaire</h3>
          <!-- <div class="card-toolbar">
              <div class="example-tools justify-content-center">
                <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
              </div>
          </div> -->
        </div>
        <!--begin::Form-->
        <form class="form" method="POST" action="{{route('admin.questionnaire.update', $questionObj->id)}}">
          <div class="card-body">
            @csrf
            @method('put')
               <div class="row justify-content-center">
                  <div class="col-md-6" id="">
                     <div class="form-group">
                        <label>Question</label>
                        <input type="text" name="title" id="title" class="form-control form-control-solid" value="{{($questionObj->title)? $questionObj->title : old('title')}}" placeholder="Enter your question" required/>
                        @if ($errors->has("title"))
                           <span class="form-text text-danger">
                              {{ $errors->first("title") }}
                           </span>
                        @endif
                     </div>
					
					<div class="form-group">
						<label>Ordering</label>
						<input type="text" name="ordering" id="myTextBox" class="form-control disabled-inputs"  value="{{$questionObj->ordering}}" placeholder="Enter Ordering">
						 @if ($errors->has("ordering"))
                           <span class="form-text text-danger">
                              {{ $errors->first("ordering") }}
                           </span>
                        @endif
					</div>	
                                
<!--                   </div> -->

                    <div class="new-fields-html d-none">
                        <div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="text" name="more[answer][]" class="form-control form-control-solid" value="{{old('answer')}}" placeholder="Enter answer">
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
                                    <input type="text" name="more[answer][]" class="form-control point" value="{{old('answer')}}" placeholder="Enter answer">
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
                    <!-- On the time of edit disabled-input  -->
					<div id="importResponse">
                    @if($questionAnswers)
                    @foreach($questionAnswers as $data)                    
                    <div class="new_fields_edit">
                        <div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="text" name="more[answer][]" class="form-control  disabled-inputd" value="{{$data->title}}" placeholder="Enter answer">
                                </div>
                                <div class="col-md-3">
                                    <input id="point" type="number" class="form-control  disabled-inputd" name="more[point][]" value="{{ $data->point }}" placeholder="Points">
                                </div>
                                <div class="col-md-3">
                                    <a href="javascript:void(0);" class="add_new_row d-none btn btn-primary">Add More</a>
                                    <a href="javascript:void(0);" data-id="{{$data->id}}" class="remove_edit_row btn btn-danger">Remove</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif 
                    </div>					
                  </div>
              
                  
			 </div>
              
               <!-- end profile -->
             </div>			
            <div class="card-footer">
                <div class="col-sm-12 text-center">
                  <button type="submit" class="btn btn-primary mr-2">Submit</button>
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
	   removeAnswer();
	   
	  
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
	  
     function removeAnswer(){
	    $(document).on('click', '.remove_edit_row', function(e){
            var itemID=$(this).attr('data-id');
            console.log(itemID);
            $.ajax({
                type:"delete",
                url:"{{url('admin/destroyAnswerById')}}/"+itemID,
				"headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                //data: "user_id="+userId,
                    //"_token = <?php //echo csrf_token() ?>",
                success: function( data ) {
                    if(data.statusCode >= 200){
                        toastr.success(data.message);
						$("#importResponse").load(location.href + " #importResponse");
                    }
                    if(data.statusCode >= 422){
                        toastr.error(data.message);
                    }
                    // $("#VendorsTable").DataTable().ajax.reload();                      
                },
                error: function (data) {
                    toastr.error('Error:', data);
                }
            });
        });
	 }	
	


	$('#myTextBox').bind('keyup paste', function(){
        this.value = this.value.replace(/[^0-9]/g, '');
  });
   </script>
@endpush
