<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8" />
    <title>Pherapeutic</title>
    <meta name="description" content="Pherapeutic" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

    <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
            @include('includes.admin.css')
        @stack('page_style')
        
        @include('includes.admin.top_script')            
</head>
<body>
    <div id="app">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
