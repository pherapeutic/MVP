@extends('layouts.admin')

@section('title') Setting @endsection

@section('content')
<div class="row">
   <div class="col-lg-12">
      <div class="card card-custom gutter-b example example-compact">
         <div class="card-header">
            <h3 class="card-title">Setting</h3>
         </div>
         <!--begin::Form-->
         <form class="form" method="POST" action="{{route('admin.settings.store')}}">
            <div class="card-body">
               @csrf
               <div class="row justify-content-center">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Application Charge %</label>
                        <input type="number" name="app_charge" id="app_charge" class="form-control form-control-solid" value="{{old('app_charge')}}" placeholder="Enter app charge in %" required/>
                        @if ($errors->has("app_charge"))
                           <span class="form-text text-danger">
                              {{ $errors->first("app_charge") }}
                           </span>
                        @endif
                     </div>
<!--                   </div> -->
                  </div>
               </div>
              
               <!-- end profile -->
            </div>

            <div class="card-footer">
              <div class="col-sm-12 text-center">              
                <button type="submit" class="btn btn-primary mr-2">Save</button>
                <a href="{{url('admin')}}" class="btn btn-secondary">Back</a>
              </div>
            </div>
         </form>
         <!--end::Form-->
      </div>
   </div>
</div>

@endsection
