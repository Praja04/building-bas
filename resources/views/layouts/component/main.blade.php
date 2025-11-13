   <!DOCTYPE html>
   <html lang="en" data-skin="default" data-bs-theme="light" data-menu-color="light" data-topbar-color="light"
       data-layout-position="fixed" data-sidenav-size="condensed" class="sidebar-enable">

   <head>
       <meta charset="utf-8">
       <title>@yield('title') || {{ env('APP_NAME') }}</title>
       <meta name="viewport" content="width=device-width, initial-scale=1">
       <meta content="{{ csrf_token() }}" name="csrf-token">

       <!-- App favicon -->
       <link rel="shortcut icon" href="{{ asset('assets') }}/images/logos/bas.ico" type="image/x-icon">

       <!-- Theme Config Js -->
       <script src="{{ asset('assets') }}/js/config.js"></script>

       <!-- Vendor css -->
       <link href="{{ asset('assets') }}/css/vendors.min.css" rel="stylesheet" type="text/css">

       <!-- App css -->
       <link href="{{ asset('assets') }}/css/app.min.css" rel="stylesheet" type="text/css">

       <!-- Datatables css -->
       <link href="{{ asset('assets') }}/plugins/datatables/responsive.bootstrap5.min.css" rel="stylesheet"
           type="text/css">

       <!-- Sweet Alert css-->
       <link href="{{ asset('assets') }}/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">

       <!-- Sweet Alerts js -->
       <script src="{{ asset('assets') }}/plugins/sweetalert2/sweetalert2.min.js"></script>

       <!-- Sweet alert demo js-->
       <script src="{{ asset('assets') }}/js/pages/misc-sweetalerts.js"></script>

       <script src="https://code.jquery.com/jquery-3.7.1.min.js"
           integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

       @yield('style')
   </head>

   <body>
       <!-- Begin page -->
       <div class="wrapper">

           <!-- Sidenav Menu Start -->
           @include('layouts.component.sidebar')
           <!-- Sidenav Menu End -->

           <!-- Topbar Start -->
           @include('layouts.component.topbar')
           <!-- Topbar End -->



           <!-- ============================================================== -->
           <!-- Start Main Content -->
           <!-- ============================================================== -->

           <div class="content-page">

               <div class="container-fluid">


                   @yield('content')


               </div>
               <!-- container -->

               <!-- Footer Start -->
               @include('layouts.component.footer')
               <!-- end Footer -->

           </div>

           <!-- ============================================================== -->
           <!-- End of Main Content -->
           <!-- ============================================================== -->

       </div>
       <!-- END wrapper -->

       <!-- Vendor js -->
       <script src="{{ asset('assets') }}/js/vendors.min.js"></script>

       <!-- App js -->
       <script src="{{ asset('assets') }}/js/app.js"></script>

       <!-- Jquery for Datatables-->
       <script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>

       <!-- Datatables js -->
       <script src="{{ asset('assets') }}/plugins/datatables/dataTables.min.js"></script>
       <script src="{{ asset('assets') }}/plugins/datatables/dataTables.bootstrap5.min.js"></script>
       <script src="{{ asset('assets') }}/plugins/datatables/dataTables.responsive.min.js"></script>
       <script src="{{ asset('assets') }}/plugins/datatables/responsive.bootstrap5.min.js"></script>

       <script>
           new DataTable('[data-tables="basic"]', {
               language: {
                   paginate: {
                       first: '<i class="ti ti-chevrons-left"></i>', // Tabler First
                       previous: '<i class="ti ti-chevron-left"></i>', // Tabler Prev
                       next: '<i class="ti ti-chevron-right"></i>', // Tabler Next
                       last: '<i class="ti ti-chevrons-right"></i>' // Tabler Last
                   }
               }
           });
       </script>

       @yield('script')
   </body>

   </html>
