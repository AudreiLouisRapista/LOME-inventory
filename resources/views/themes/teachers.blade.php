<!DOCTYPE html>
<html lang="en">

{{-- 1. HEAD Partial --}}
@include('layout.partials.head')

<body class="hold-transition sidebar-mini layout-fixed">
    {{-- Auth Check - This logic should ideally be in Middleware or a Controller... --}}
    @if (session('id') == null)
        {{-- If the ID is missing, the user is logged out. Redirect immediately. --}}
        @php
            // Redirect to the login page
            header('Location: /'); // <-- Ensure /login is your correct route
            exit();
        @endphp
    @endif
    <div class="wrapper">

        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTELogo" height="60"
                width="60">
        </div>

        {{-- 2. NAVBAR Partial --}}
        @include('layout.partials.teacher_navbar')



        {{-- 4. CONTENT WRAPPER: This is where the extending page content goes --}}
        <div class="content">
            {{-- @yield('content_header') is often used here for the breadcrumbs --}}
            @yield('content_header')

            <section class="content">
                <div class="container-fluid">
                    {{-- THIS IS THE MAIN CONTENT SECTION --}}
                    @yield('content')
                </div>
            </section>
        </div>
        {{-- /.content-wrapper --}}



    </div>
    {{-- ./wrapper --}}

    {{-- 6. SCRIPT Partial --}}
    @include('layout.partials.script')

</body>

</html>
