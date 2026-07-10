<!DOCTYPE html>
<html>
  <head>
    
    <meta name="yandex-verification" content="49fc59dd862e365d" />
    <meta content="utf-8" http-equiv="encoding" charset=UTF-8>
    <title>Flying Calculation</title>
    <link rel="shortcut icon" href="#">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery.fileupload.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery.fileupload-ui.css') }}" rel="stylesheet">
    <noscript><link rel="stylesheet" href="{{ asset('/css/jquery.fileupload-noscript.css') }}"></noscript>
    <noscript><link rel="stylesheet" href="{{ asset('/css/jquery.fileupload-ui-noscript.css') }}"></noscript>
    <link rel="stylesheet" href="/font-awesome/css/font-awesome.min.css" type="text/css">
    <link href="{{ asset('/css/slick/slick.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/slick/slick-theme.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  
    <link href="{{ asset('/plugins/datatables/dataTables.bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/plugins/select2/select2.min.css') }}">
    <link href="{{ asset('/css/theme.min.css') }}" rel="stylesheet">
    
    <link href="{{ asset('/css/all-skins.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/iCheck/all.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/datepicker.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/editor.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/flying_calculation_admin.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery-ui.min.css') }}" rel="stylesheet">
    <script src="{{ asset('/js/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/jquery-ui.min.js') }}"></script>
    <script>
      $(document).ajaxError(function(event, xhr) {
        if (xhr.status === 401) {
          window.location.href = '/';
        }
      });
    </script>
    <script src="{{ asset('/js/bootstrap-timepicker.js') }}"></script>
    <link rel="shortcut icon" href="/images/favicon.ico">
  </head>
	<body class="hold-transition skin-red sidebar-mini">
    <div class="wrapper">
      <header class="main-header">
       
        <a href="/" class="logo">
          <span class="logo-mini"><b>FC</b></span>
          <span class="logo-lg"><b>Flying Calculation</b></span>
        </a>
        <nav class="navbar navbar-static-top">
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">            
             
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <span class="hidden-xs"><i class="fa fa-user  sm-padding-right"></i>&nbsp;<?php echo strtoupper(Auth::user()->username)?></span>
                </a>
                <ul class="dropdown-menu">
                  <li class="user-header">
                  <p>
                    <?php 
                      $name = Auth::user()->name;
                      $user_type = Auth::user()->user_type;
                      $image = Auth::user()->image;
                    ?>
                    <?php echo strtoupper($name)?>
                    @if($user_type ==0)
                      <small>Admin</small>
                    @else
                      <small>User</small>
                    @endif
                  </p>
                  <?php echo '<img src="/images/'. $image .'" class="img-circle" alt="User Image"" />'; ?>
                  </li>
                  <li class="user-footer">
                    
                    <div class="text-center">
                      <a href="/user/logout" class="btn btn-default btn-flat">Log out</a>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <aside class="main-sidebar">
        <section class="sidebar">
          <ul class="sidebar-menu">
         
            <li class="treeview <?php if (isset($menu) && $menu == 'users') {echo "active";} ?>">
              <a href="#">
                <i class="fa fa-user text-danger"></i><span>Users</span><i class="fa fa-angle-right pull-right"></i>
              </a>
              <ul class="treeview-menu <?php if (isset($menu) && $menu == 'users') {echo "menu-open";} ?>">
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'view_users') {echo "active";} ?>"><a href="{{ url('/user') }}"><i class="fa fa-table text-white"></i>View Users</a></li>
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'add_user') {echo "active";} ?>"><a href="{{ url('/user/add') }}"><i class="fa fa-plus text-white"></i>Add User</a></li>
              </ul>
            </li>
            <!-- <li class="treeview <?php if (isset($menu) && $menu == 'mail_contents') {echo "active";} ?>">
              <a href="#">
                <i class="fa fa-circle-o text-danger"></i><span>Mail Content</span><i class="fa fa-angle-right pull-right"></i>
                <ul class="treeview-menu <?php if (isset($menu) && $menu == 'mail_contents') {echo "menu-open";} ?>">
                  <li class="<?php if (isset($sub_menu) && $sub_menu == 'view_mail_contents') {echo "active";} ?>"><a href="{{ url('/mail-content') }}"><i class="fa fa-table text-white"></i>View Mail Contents</a></li>
                </ul>
                <i class="fa fa-angle-right pull-right"></i>
              </a>
            </li> -->
            <!-- <li class="treeview <?php if (isset($menu) && $menu == 'bookings') {echo "active";} ?>">
              <a href="#">
                <i class="fa fa-circle-o text-danger"></i><span>Bookings</span><i class="fa fa-angle-right pull-right"></i>
                <ul class="treeview-menu <?php if (isset($menu) && $menu == 'bookings') {echo "menu-open";} ?>">
                  <li class="<?php if (isset($sub_menu) && $sub_menu == 'view_bookings') {echo "active";} ?>"><a href="{{ url('/booking') }}"><i class="fa fa-table text-white"></i>View Bookings</a></li>
                  <li class="<?php if (isset($sub_menu) && $sub_menu == 'view_booking_points') {echo "active";} ?>"><a href="{{ url('/booking/points') }}"><i class="fa fa-table text-white"></i>Booking Points</a></li>
                </ul>
                <i class="fa fa-angle-right pull-right"></i>
              </a>
            </li> -->
            <li class="treeview <?php if (isset($menu) && $menu == 'cities') {echo "active";} ?>">
              <a href="#">
                <i class="fa fa-circle-o text-danger"></i><span>Cities</span><i class="fa fa-angle-right pull-right"></i>
              </a>
              <ul class="treeview-menu <?php if (isset($menu) && $menu == 'cities') {echo "menu-open";} ?>">
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'view_cities') {echo "active";} ?>"><a href="{{ url('/city') }}"><i class="fa fa-table text-white"></i>View Cities</a></li>
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'add_city') {echo "active";} ?>"><a href="{{ url('/city/add') }}"><i class="fa fa-plus text-white"></i>Add City</a></li>
              </ul>
            </li>
            <li class="treeview <?php if (isset($menu) && $menu == 'airports') {echo "active";} ?>">
              <a href="#">
                <i class="fa fa-circle-o text-danger"></i><span>Airports</span><i class="fa fa-angle-right pull-right"></i>
              </a>
              <ul class="treeview-menu <?php if (isset($menu) && $menu == 'airports') {echo "menu-open";} ?>">
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'view_airports') {echo "active";} ?>"><a href="{{ url('/airport') }}"><i class="fa fa-table text-white"></i>View Airports</a></li>
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'add_airport') {echo "active";} ?>"><a href="{{ url('/airport/add') }}"><i class="fa fa-plus text-white"></i>Add Airport</a></li>
              </ul>
            </li>
            <li class="treeview <?php if (isset($menu) && $menu == 'planes') {echo "active";} ?>">
              <a href="#">
                <i class="fa fa-circle-o text-danger"></i><span>Machines</span><i class="fa fa-angle-right pull-right"></i>
              </a>
              <ul class="treeview-menu <?php if (isset($menu) && $menu == 'planes') {echo "menu-open";} ?>">
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'view_planes') {echo "active";} ?>"><a href="{{ url('/plane') }}"><i class="fa fa-table text-white"></i>View Machines</a></li>
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'add_plane') {echo "active";} ?>"><a href="{{ url('/plane/add') }}"><i class="fa fa-plus text-white"></i>Add Machine</a></li>
              </ul>
            </li>
            <li class="treeview <?php if (isset($menu) && $menu == 'planes_subtype') {echo "active";} ?>">
              <a href="#">
                <i class="fa fa-circle-o text-danger"></i><span>Plane Sub-type</span><i class="fa fa-angle-right pull-right"></i>
              </a>
              <ul class="treeview-menu <?php if (isset($menu) && $menu == 'planes_subtype') {echo "menu-open";} ?>">
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'view_planes_subtype') {echo "active";} ?>"><a href="{{ url('/subtype') }}"><i class="fa fa-table text-white"></i>View Sub-type</a></li>
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'add_plane_subtype') {echo "active";} ?>"><a href="{{ url('/subtype/add') }}"><i class="fa fa-plus text-white"></i>Add Sub-type</a></li>
              </ul>
            </li>
            <li class="treeview <?php if (isset($menu) && $menu == 'handling_charge') {echo "active";} ?>">
              <a href="#">
                <i class="fa fa-circle-o text-danger"></i><span>General Handling Charge</span><i class="fa fa-angle-right pull-right"></i>
              </a>
              <ul class="treeview-menu <?php if (isset($menu) && $menu == 'handling_charge') {echo "menu-open";} ?>">
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'view_handling_charge') {echo "active";} ?>"><a href="{{ url('/handling-charge') }}"><i class="fa fa-table text-white"></i>View Handling Charge</a></li>
              </ul>
            </li>
            <li class="treeview <?php if (isset($menu) && $menu == 'settings') {echo "active";} ?>">
              <a href="#">
                <i class="fa fa-circle-o text-danger"></i><span>Tax & medical team cost</span><i class="fa fa-angle-right pull-right"></i>
              </a>
              <ul class="treeview-menu <?php if (isset($menu) && $menu == 'settings') {echo "menu-open";} ?>">
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'view_settings') {echo "active";} ?>"><a href="{{ url('/setting') }}"><i class="fa fa-table text-white"></i>View Settings</a></li>
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'add_setting') {echo "active";} ?>"><a href="{{ url('/setting/add') }}"><i class="fa fa-plus text-white"></i>Add Setting</a></li>
              </ul>
            </li>
            <li class="treeview <?php if (isset($menu) && $menu == 'route') {echo "active";} ?>">
              <a href="#">
                <i class="fa fa-circle-o text-danger"></i><span>Route</span><i class="fa fa-angle-right pull-right"></i>
              </a>
              <ul class="treeview-menu <?php if (isset($menu) && $menu == 'route') {echo "menu-open";} ?>">
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'view_routes') {echo "active";} ?>"><a href="{{ url('/route') }}"><i class="fa fa-table text-white"></i>View Routes</a></li>
                <li class="<?php if (isset($sub_menu) && $sub_menu == 'add_route') {echo "active";} ?>"><a href="{{ url('/route/add') }}"><i class="fa fa-plus text-white"></i>Add Route</a></li>
              </ul>
            </li>
            <!-- <li>
              <a href="/get_ground_time">
                <i class="fa fa-circle-o text-danger"></i><span>Set Ground Time</span>
              </a>
            </li> -->
            <li>
              <a href="/airport_list">
                <i class="fa fa-circle-o text-primary"></i><span>Get Airports</span>
              </a>
            </li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>
      <div class="content-wrapper">
        @include('partials.flash_toasts')
        @yield('content') 
      </div>
      <!-- <footer class="main-footer">
        <strong>Copyright &copy; 2018</strong> All rights reserved.
      </footer> -->
    </div>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
		<!-- Bootstrap 3.3.6 -->
		<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
		<!-- AdminLTE App -->
		<script src="{{ asset('/js/app.min.js') }}"></script>
		<!-- SlimScroll -->
		<script src="{{ asset('/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- Datepicker JS -->  
		<script src="{{ asset('/js/bootstrap-datepicker.js') }}"></script>
		<!-- Data tables -->
		<script src="{{ asset('/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('/plugins/select2/select2.full.min.js') }}"></script>
    <!-- icheck -->
    <script src="{{ asset('/plugins/iCheck/icheck.min.js') }}"></script>  
    <!-- Slick -->
    <script src="{{ asset('/js/slick/slick.min.js') }}"></script> 
		<!-- FastClick -->
		<script src="{{ asset('/plugins/fastclick/fastclick.js') }}"></script>
    <script src="{{ asset('/plugins/chartjs/Chart.min.js') }}"></script>  
    <!-- Jquery File Upload JS -->  
    <script src="{{ asset('/js/vendor/jquery.ui.widget.js') }}"></script>
    <script src="{{ asset('/js/jquery.fileupload.js') }}"></script>
    <script src="{{ asset('/js/jquery.iframe-transport.js') }}"></script>
    <script src="{{ asset('/js/jquery.fileupload-process.js') }}"></script>
    <script src="{{ asset('/js/jquery.fileupload-image.js') }}"></script>
    <script src="{{ asset('/js/jquery.fileupload-validate.js') }}"></script>
    <script src="{{ asset('/js/flying_calculation_file_upload.js') }}"></script>
    <script src="{{ asset('/js/editor.js') }}"></script>
    <!-- Datarange Picker -->
    <script src="{{ asset('/js/moment.js') }}"></script>
    <script src="{{ asset('/js/graph_demo.js') }}"></script>
		<script src="{{ asset('/js/daterangepicker.js') }}"></script>
		
	</body>
</html>
