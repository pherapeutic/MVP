@extends('layouts.admin')

@section('title') Change Password @endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><b>Change Password</b></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    @csrf
                    <div class="form-group">
                    <div class="row">
                    <div class="col-md-3">
                    <label for="exampleInputPassword1" class="float-right">Password</label>
                    </div>
                    <div class="col-md-6">
                    <input name="password" type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                     @if ($errors->has('password'))
                              <span class="form-text text-danger">{{ $errors->first('password') }}</span>
                        @endif
                  </div>
                  </div>
                </div>
                  <div class="form-group">
                    <div class="row">
                    <div class="col-md-3">
                    <label for="exampleInputPassword2" class="float-right">Confirm Password</label>
                  </div>
                    <div class="col-md-6">
                    <input name="confirm_password" type="password" class="form-control" id="exampleInputPassword2" placeholder="Confirm Password">
                    @if ($errors->has('confirm_password'))
                              <span class="form-text text-danger">{{ $errors->first('confirm_password') }}</span>
                        @endif


                  </div>
                    </div>
                  </div>
                
                  <div class="col-md-9">
                  <button type="submit" class="btn btn-primary float-right">Submit</button>
                </div>
                   

                   
                  

                    
                </form>
            </div>
        </div>
    </div>
</div>





@endsection

