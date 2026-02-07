<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My App | @yield('title', 'Dashboard')</title>


    <link rel="icon" type="image/png" href="{{ asset('images/SystemAethelLogo.png') }}">

    <link rel="stylesheet"
        href="{{ asset('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('scss/table.scss') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_dp.css') }}">




    <!-- ✅ Add this line -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    @yield('styles')

    <style>
        /* Smooth transitions when switching */
        body,
        .content-wrapper,
        .card,
        .table,
        .nav-link {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Fix for tables in Dark Mode */
        .dark-mode .table {
            background-color: #121212 !important;
            color: #e0e0e0 !important;
        }

        /* Fix for cards in Dark Mode */
        .dark-mode .card {
            background-color: #1f1f1f !important;
            border-color: #333 !important;
            color: #fff !important;
        }

        /* Keep your existing Black Sidebar consistent */
        .dark-mode .main-sidebar {
            background-color: #000 !important;
            border-right: 1px solid #333;
        }
    </style>
</head>
