@extends('layouts.admin')

@section('title') Create new therapist @endsection

@section('content')
<div class="row">
   <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Add New Therapist</h3>
            <!-- <div class="card-toolbar">
               <div class="example-tools justify-content-center">
                  <span class="example-toggle" data-toggle="tooltip" title="View code"></span>
                  <span class="example-copy" data-toggle="tooltip" title="Copy code"></span>
               </div>
            </div> -->
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('admin.therapist.store')}}">
            <div class="card-body">
               @csrf
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control form-control-solid" value="{{old('first_name')}}" placeholder="Enter first name" required/>
                        @if ($errors->has("first_name"))
                           <span class="form-text text-danger">
                              {{ $errors->first("first_name") }}
                           </span>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control form-control-solid" value="{{old('last_name')}}" placeholder="Enter last name" required/>
                        @if ($errors->has("last_name"))
                              <span class="form-text text-danger">
                              {{ $errors->first("last_name") }}
                              </span>
                        @endif
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control form-control-solid" value="{{old('email')}}" placeholder="Enter email" required/>
                        @if ($errors->has("email"))
                           <span class="form-text text-danger">
                              {{ $errors->first("email") }}
                           </span>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <!-- start profile -->
                     <div class="form-group">
                        <label>Address</label>
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}"/>
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}"/>
                        <input type="text" id="address" name="address" class="form-control form-control-solid" value="{{ old('address') }}" placeholder="Enter Address" required/>
                        @if ($errors->has("address"))
                              <span class="form-text text-danger">
                              {{ $errors->first("address") }}
                              </span>
                        @elseif ($errors->has("latitude"))
                              <span class="form-text text-danger">
                              {{ $errors->first("latitude") }}
                              </span>
                        @elseif ($errors->has("longitude"))
                              <span class="form-text text-danger">
                              {{ $errors->first("longitude") }}
                              </span>
                        @endif
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control form-control-solid" placeholder="Password" required/>
                        @if ($errors->has("password"))
                              <span class="form-text text-danger">
                              {{ $errors->first("password") }}
                              </span>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control form-control-solid" placeholder="Confirm Password" required/>
                        @if ($errors->has("confirm_password"))
                              <span class="form-text text-danger">
                              {{ $errors->first("confirm_password") }}
                              </span>
                        @endif
                        
                     </div>
                  </div>
               </div>
               
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Qualification</label>
                        <input type="text" name="qualification" class="form-control form-control-solid" value="{{old('qualification')}}" placeholder="Enter Qualification" required/>
                        @if ($errors->has("qualification"))
                              <span class="form-text text-danger">
                              {{ $errors->first("qualification") }}
                              </span>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Experience (In Years)</label>
                        <input type="number" name="experience" class="form-control form-control-solid" value="{{old('experience')}}" placeholder="Enter Experience" min="0" step="any" required/>
                        @if ($errors->has("experience"))
                              <span class="form-text text-danger">
                              {{ $errors->first("experience") }}
                              </span>
                        @endif
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Specialism</label>
                        <select class="form-control select2" id="specialisms" name="specialisms[]" multiple="multiple" required>
                           @foreach(getTherapistTypes() as $therapistTypeId => $therapistType)
                              <option value="{{ $therapistTypeId }}" {{ (old('specialisms') && in_array($therapistTypeId, old('specialisms'))) ? ('selected="selected"') : ('') }}>{{ $therapistType }}</option>
                           @endforeach
                        </select>
                        @if ($errors->has('specialisms'))
                              <span class="form-text text-danger">{{ $errors->first('specialisms') }}</span>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Select languages which therapist can speak</label>
                        <select class="form-control select2" id="languages" name="languages[]" multiple="multiple" required>
                           @foreach(getLanguages() as $languageId => $language)
                              <option value="{{ $languageId }}" {{ (old('languages') && in_array($languageId, old('languages'))) ? ('selected="selected"') : ('') }}>{{ $language }}</option>
                           @endforeach
                        </select>
                        @if ($errors->has('languages'))
                              <span class="form-text text-danger">{{ $errors->first('languages') }}</span>
                        @endif
                     </div>
                  </div>
               </div>
               
               <!-- end profile -->
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

@endsection

@push('page_script')
   <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyAYPbdVQNA2a_iUyiCYAGr0Xdced_kZrKU&libraries=places" ></script>
   <script src="{{ asset('assets/js/pages/crud/forms/widgets/select2.js') }}"></script>

   <script>
      $('#languages').select2({
         placeholder: "Select languages",
      });

      $('#specialisms').select2({
         placeholder: "Select specialisms",
      });

      function initialize() {
         var address = (document.getElementById('address'));
         var autocomplete = new google.maps.places.Autocomplete(address);
         autocomplete.setTypes(['geocode']);
         google.maps.event.addListener(autocomplete, 'place_changed', function() {
                  var place = autocomplete.getPlace();
                  if (!place.geometry) {
                     return;
                  }

               var address = '';
               if (place.address_components) {
                  address = [
                     (place.address_components[0] && place.address_components[0].short_name || ''),
                     (place.address_components[1] && place.address_components[1].short_name || ''),
                     (place.address_components[2] && place.address_components[2].short_name || '')
                     ].join(' ');
               }
               /*********************************************************************/
               /* var address contain your autocomplete address *********************/
               /* place.geometry.location.lat() && place.geometry.location.lat() ****/
               /* will be used for current address latitude and longitude************/
               /*********************************************************************/
               
         // document.getElementById('lat').innerHTML = place.geometry.location.lat();
         // document.getElementById('long').innerHTML = place.geometry.location.lng();
               $("#latitude").val(place.geometry.location.lat());
               $("#longitude").val(place.geometry.location.lng());
         });
      }

      google.maps.event.addDomListener(window, 'load', initialize);
   </script>
@endpush
