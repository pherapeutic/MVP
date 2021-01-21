@extends('layouts.auth')

@section('content')
<!-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->

<!--begin::Aside-->
<div class="login-aside d-flex flex-column flex-row-auto" style="background-color: #F2C98A;">
    <!--begin::Aside Top-->
    <div class="d-flex flex-column-auto flex-column pt-lg-30 pt-15">
        <!--begin::Aside header-->
        <a href="{{ route('admin.home') }}" class="text-center mb-10">
            <img src="{{ asset('assets/media/logos/pherapeutic.png') }}" class="max-h-70px" alt="" />
        </a>
        <!--end::Aside header-->
        <!--begin::Aside title-->
        <!-- <h3 class="font-weight-bolder text-center font-size-h4 font-size-h1-lg" style="color: #986923;">Discover Amazing Metronic
        <br />with great build tools</h3> -->
        <!--end::Aside title-->
    </div>
    <!--end::Aside Top-->
    <!--begin::Aside Bottom-->
    <div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center" style="background-image: url(assets/media/svg/illustrations/login-visual-1.svg)"></div>
    <!--end::Aside Bottom-->
</div>
<!--begin::Aside-->
<!--begin::Content-->
<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
    <!--begin::Content body-->
    <div class="d-flex flex-column-fluid flex-center">
        <!--begin::Signin-->
        <div class="login-form login-signin">
            <!--begin::Form-->
            <form class="form" method="POST" action="{{ route('login') }}">
                <!--begin::Title-->
                @csrf
                <div class="pb-13 pt-lg-0 pt-5">
                    <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg text-center">Admin Login</h3>
                    
                </div>
                <!--begin::Title-->
                <!--begin::Form group-->
                <div class="form-group">
                    <label class="font-size-h6 font-weight-bolder text-dark">Email</label>
                    <input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg @error('email') is-invalid @enderror" type="email" name="email" required autocomplete="email" autofocus />
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <!--end::Form group-->
                <!--begin::Form group-->
                <div class="form-group">
                    <div class="d-flex justify-content-between mt-n5">
                        <label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>
                        
                    </div>
                    <input class="form-control form-control-solid h-auto py-6 px-6 rounded-lg @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password" />
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <!--end::Form group-->
                <!--begin::Action-->
                <div class="pb-lg-0 pb-5">
                    <input class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3" type="submit" name="submit" value="Sign In">
                </div>
                <!--end::Action-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Signin-->
    </div>
    <!--end::Content body-->
    <!--begin::Content footer-->
    <div class="d-flex justify-content-lg-start justify-content-center align-items-end py-7 py-lg-0">
        <div class="text-dark-50 font-size-lg font-weight-bolder mr-10 ">
            <span class="mr-1">2020©</span>
            <a href="{{ route('admin.home') }}" class="text-dark-75 text-hover-primary">Pherapeutic</a>
            <a href="privacy" class="text-dark-75 text-hover-primary">Privacy & Policy</a>

        </div>
    </div>
    <!--end::Content footer-->
</div>
<!--end::Content-->
@endsection
