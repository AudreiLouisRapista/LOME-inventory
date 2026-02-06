 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>AdminLTE 3 | Navbar & Tabs</title>

     <!-- Google Font: Source Sans Pro -->
     <link rel="stylesheet"
         href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
     <!-- Font Awesome -->
     <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
     <!-- SweetAlert2 -->
     <link rel="stylesheet" href="../../plugins/sweetalert2/sweetalert2.min.css">
     <!-- Toastr -->
     <link rel="stylesheet" href="../../plugins/toastr/toastr.min.css">
     <!-- Theme style -->
     <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
 </head>

 <body class="hold-transition sidebar-mini">
     <div class="wrapper">
         <!-- Navbar -->
         <nav class="main-header navbar navbar-expand navbar-white navbar-light">
             <!-- Left navbar links -->
             <ul class="navbar-nav">
                 <li class="nav-item">
                     <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                             class="fas fa-bars"></i></a>
                 </li>
                 <li class="nav-item d-none d-sm-inline-block">
                     <a href="{{ route('admin.dashboard') }}" class="nav-link">Home</a>
                 </li>

             </ul>

             <ul class="navbar-nav ml-auto">
                 <li class="nav-item">
                     <a class="nav-link" href="#" role="button" onclick="toggleDarkMode()">
                         <i class="bi bi-cloud-moon"></i>
                     </a>
                 </li>
             </ul>

         </nav>
         <!-- /.navbar -->
         <style>
             i.bi-cloud-moon {
                 font-size: 30px;
                 color: #3d3c3c;
                 transition: color 0.3s ease;
                 position: relative;
                 bottom: 50%;
                 right: 100%;
             }

             i.bi-cloud-moon:hover {
                 color: #020202;
             }
         </style>
