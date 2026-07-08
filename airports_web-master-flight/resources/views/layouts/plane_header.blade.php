<!DOCTYPE html>
<html>
<head>

	<meta content="utf-8" http-equiv="encoding">
  <title>Flying Calculation</title>
  <link rel="shortcut icon" href="/images/favicon.ico">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	
	<link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
  <link href='https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>	
  <link href="{{ asset('/css/magnific-popup.css') }}" rel="stylesheet">
  <link href="{{ asset('/css/datepicker.css') }}" rel="stylesheet" type="text/css">
  <!-- Bootstrap time Picker -->
  <link href="{{ asset('/plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet"> 
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('/plugins/select2/select2.min.css') }}">
  <link href="{{ asset('/css/editor.css') }}" rel="stylesheet">
  <link href="{{ asset('/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
  <!-- Theme CSS -->
	<link href="{{ asset('/css/creative.min.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/welcome.css') }}" rel="stylesheet">
  
	
	<!-- jQuery 2.2.0 -->
	<script src="{{ asset('/js/jquery.min.js') }}"></script>
  <script>
    $(document).ajaxError(function(event, xhr) {
      if (xhr.status === 401) {
        window.location.href = '/';
      }
    });
  </script>
  <script src="{{ asset('/js/bootstrap-datetimepicker.min.js') }}"></script>
  <script src="{{ asset('/js/moment.js') }}"></script>
</head>

<body id="plane-page-top">
  <nav id="mainNav" class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
      <div class="navbar-header">
          <a class="navbar-brand page-scroll" <?php if(isset($_GET["plane-id"])) {echo "href='".URL::previous()."'";} else {echo "href='/'";} ?>><img src="/img/back_arrow.png" class="back-arrow img-responsive"/></a>
      </div>
		
    </div>
  </nav> 
  @yield('content')
  <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('/js/jquery.easing.min.js') }}"></script>
  <script src="{{ asset('/js/bootstrap-datepicker.js') }}"></script>
  <!-- Select2 -->
  <script src="{{ asset('/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('/js/editor.js') }}"></script>
  <!-- Plugin JavaScript -->
  <script src="{{ asset('/js/scrollreveal.min.js') }}"></script>
  <script src="{{ asset('/js/jquery.magnific-popup.min.js') }}"></script>
  <!-- Theme JavaScript -->
  <script src="{{ asset('/js/creative.min.js') }}"></script>
  <script src="{{ asset('/js/flying_calculation.js') }}"></script>
</body>
</html>
