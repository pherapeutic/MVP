<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <base href="">
        @include('includes.meta')  
        
        @include('includes.auth.css')
        
        @stack('page_style')
    
    </head> 
    <body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading"> 
        
        <!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
                @yield('content')                     
            </div>
			<!--end::Login-->
		</div>
		<!--end::Main-->

        @include('includes.admin.bottom_script')
        
        @stack('page_script')
    </body>
</html> 