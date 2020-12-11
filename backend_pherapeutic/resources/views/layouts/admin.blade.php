<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <base href="">
        @include('includes.meta')  
        
        @include('includes.admin.css')
        
        @stack('page_style')
        
        @include('includes.admin.top_script')
    
    </head> 
    <body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading"> 
        
        @include('includes.admin.mobile_header')
        <!--begin::Main-->
        <div class="d-flex flex-column flex-root">
			<!--begin::Page-->
			<div class="d-flex flex-row flex-column-fluid page">
                @include('includes.admin.sidebar')
                <!--begin::Wrapper-->
				<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                    @include('includes.admin.header')
                        
                        <!--begin::Content-->
                            <div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
                                <!--begin::Entry-->
                                <div class="d-flex flex-column-fluid">
                                    <!--begin::Container-->
                                    <div class="container">
                                        <!--begin::Dashboard-->
                                            @include('includes.admin.flash_message')
                                        <!--begin::Row-->
                                            @yield('content')
                                        <!--end::Row-->
                                        </div>
                                    <!--end::Container-->
                                </div>
                                <!--end::Entry-->
                            </div>
                        <!--end::Content-->
                    @include('includes.admin.footer')
				</div>
				<!--end::Wrapper-->
            </div>
			<!--end::Page-->
		</div>
        <!--end::Main-->
        
        @include('includes.admin.right_sidebar')

        @include('includes.admin.bottom_script')
        
        @stack('page_script')
    </body>
</html> 