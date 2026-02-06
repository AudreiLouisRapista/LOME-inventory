<html lang ="{{ app()->getLocale() }}">

<link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>

<head>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        @include('partials.navbar')
        @include('partials.sidebar')

        <div class="content-wrapper" id="cwrapper-color">
            @yield('content')
        <a class="btn btn-primary back-to-top no print"
        id="back-to-top" role="button" aria-label="Scroll to top"
        href="#">
        <i class="fas fa-chevron-up"></i>
    </a>
        </div>


    </div>

</body>

</html>