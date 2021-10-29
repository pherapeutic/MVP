@extends('layouts.admin')

@section('title') Edit Profile @endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><b>Update Profile</b></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{route('admin.user.update',$model->id)}}" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                   
                <div class="card-body">
                  <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                    <label for="first_name">Name</label>
                    <input name="first_name" type="text" value="{{old('first_name') ?? $model->first_name}}" class="form-control" id="first_name" placeholder="Enter Name">
                     @if ($errors->has('first_name'))
                              <span class="form-text text-danger">{{ $errors->first('first_name') }}</span>
                        @endif
                  </div>
                  
                  
                   <div class="form-group">
                        <label>Contact No</label>
                        <input name="contact_no" type="text" value="{{old('contact_no') ?? $model->contact_no}}" class="form-control" id="contact_no" placeholder="Enter Contact No">
                         @if ($errors->has('contact_no'))
                              <span class="form-text text-danger">{{ $errors->first('contact_no') }}</span>
                        @endif
                       
                    </div>
                  </div>

                  <div class="col-md-6">
                   <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" name="email" value="{{old('email') ?? $model->email}}" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                     @if ($errors->has('email'))
                              <span class="form-text text-danger">{{ $errors->first('email') }}</span>
                        @endif
                  </div>

                  <div class="form-group">
                    <label for="image">Profile</label>
                    <input type="file" name="image" class="form-control">
                  </div>

                  <div>
                  <button type="submit" class="btn btn-primary float-right">Submit</button>
                </div>
                    
                  </div>
                </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection

