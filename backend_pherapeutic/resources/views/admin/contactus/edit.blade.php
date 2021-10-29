@extends('layouts.admin')

@section('title') Edit client @endsection

@section('content')
<div class="row">
   <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Edit Client</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('admin.client.update',$userObj->id)}}">
            <div class="card-body">
               @csrf
               @method('put')
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control form-control-solid" placeholder="Enter first name" value="{{ (old('first_name')) ? (old('first_name')) : ($userObj->first_name) }}" />
                        @if ($errors->has('first_name'))
                              <span class="form-text text-danger">
                                 {{ $errors->first('first_name') }}
                              </span>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control form-control-solid" placeholder="Enter last name" value="{{ (old('last_name')) ? (old('last_name')) : ($userObj->last_name) }}" />
                        @if ($errors->has('last_name'))
                              <span class="form-text text-danger">
                                 {{ $errors->first('last_name') }}
                              </span>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <label>Email</label>
                  <input type="email" name="email" class="form-control form-control-solid disabled-input" placeholder="Enter email"value="{{ $userObj->email }}"/>
                  @if ($errors->has('email'))
                        <span class="form-text text-danger">
                            {{ $errors->first('email') }}
                        </span>
                    @endif
               </div>

               @php
                  $languageIds = (old('languages')) ? (old('languages')) : (array());
                  if(!$languageIds){
                     $userLanguages = $userObj->userLanguages;
                     foreach($userLanguages as $key => $userLanguage){
                        array_push($languageIds, $userLanguage->language_id);
                     }
                  }
               @endphp

               <div class="form-group">
                  <label>Select languages which client can speak</label>
                  <select class="form-control select2" id="languages" name="languages[]" multiple="multiple" required>
                     @foreach(getLanguages() as $languageId => $language)
                        <option value="{{ $languageId }}" {{ ($languageIds && in_array($languageId, $languageIds)) ? ('selected="selected"') : ('') }}>{{ $language }}</option>
                     @endforeach
                  </select>
                  @if ($errors->has('languages'))
                        <span class="form-text text-danger">{{ $errors->first('languages') }}</span>
                  @endif
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

@push('page_script')
   <script src="{{ asset('assets/js/pages/crud/forms/widgets/select2.js') }}"></script>
   <script>
      $('#languages').select2({
         placeholder: "Select languages",
      });
   </script>
@endpush
