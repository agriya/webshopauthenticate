<html>
	<head>
		<title>{{ \Config::get('webshopauthenticate::page_title') }}</title>
		<meta name="description" content="{{ \Config::get('webshopauthenticate::page_meta_description') }}" />
		<meta name="keywords" content="{{ \Config::get('webshopauthenticate::page_meta_keywords') }}" />


		<!-- Mobile Specific Metas
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Favicons
		================================================== -->
		<link rel="shortcut icon" href="{{ URL::asset('packages/agriya/webshopauthenticate/images/header/favicon/favicon.ico') }}">

        <!-- CSS
		================================================== -->
        <link rel="stylesheet" href="{{ URL::asset('packages/agriya/webshopauthenticate/css/jQuery_plugins/ui-lightness/jquery-ui-1.10.3.custom.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('packages/agriya/webshopauthenticate/css/jQuery_plugins/jquery.fancyBox-v2.1.5-0/jquery.fancybox.css') }}">

	    <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="{{ URL::asset('packages/agriya/webshopauthenticate/css/bootstrap/bootstrap.min.css') }}">        <!-- // Version 3.1.1  -->
        <link rel="stylesheet" href="{{ URL::asset('packages/agriya/webshopauthenticate/css/bootstrap/bootstrap-theme.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('packages/agriya/webshopauthenticate/css/bootstrap/font-awesome.min.css') }}">     <!-- // Version 4.0.3  -->

        <link rel="stylesheet" href="{{ URL::asset('packages/agriya/webshopauthenticate/css/core/embed_fonts.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('packages/agriya/webshopauthenticate/css/core/base.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('packages/agriya/webshopauthenticate/css/core/form.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('packages/agriya/webshopauthenticate/css/core/mobile.css') }}">

        <!-- HTML5 shiv and Respond.js IE8 support of HTML5 elements and media queries // HTML5 Shiv Version - 3.7.0 // Respond.js Version - 1.4.2   -->
        <!--[if lt IE 9]>
          <script src="{{ URL::asset('packages/agriya/webshopauthenticate/js/bootstrap/html5shiv.js') }}"></script>
          <script src="{{ URL::asset('packages/agriya/webshopauthenticate/js/bootstrap/respond.min.js') }}"></script>
          <link rel="stylesheet" href="{{ URL::asset('packages/agriya/webshopauthenticate/css/core/ie.css') }}">
        <![endif]-->

        <script language="javascript">
			var package_name = "{{ Lang::get('webshopauthenticate::jobs.package_name') }}";
			var mes_required = "{{ Lang::get('webshopauthenticate::common.required') }}";
			var page_name = "";
		</script>
	</head>
	<body>
		<article class="article-container">
			<section class="container">
	            <div class="row">
	            	@if (Sentry::check())
						<a href="{{ URL::to(\Config::get('webshopauthenticate::uri').'/logout') }}" class="btn btn-info btn-sm pull-right">{{ \Lang::get('webshopauthenticate::users.logout') }}</a>
					@endif
	                <div class="col-md-12" role="main">
	                	@yield('content')
	                </div>
	            </div>
	        </section>
		</article>
		<!-- JS
		================================================== -->
    	<script src="{{ URL::asset('packages/agriya/webshopauthenticate/js/jquery-1.11.0.min.js') }}"></script>
        <script src="{{ URL::asset('packages/agriya/webshopauthenticate/js/jquery-ui-1.10.3.custom.min.js') }}"></script>
        <script src="{{ URL::asset('packages/agriya/webshopauthenticate/js/jquery.validate.min.js') }}"></script>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <script src="{{ URL::asset('packages/agriya/webshopauthenticate/js/bootstrap/bootstrap.min.js') }}"></script>

        <script src="{{ URL::asset('packages/agriya/webshopauthenticate/js/jquery.fancybox.pack.js') }}"></script>
        <script src="{{ URL::asset('packages/agriya/webshopauthenticate/js/functions.js') }}"></script>
        <script src="{{ URL::asset('packages/agriya/webshopauthenticate/js/jobs.js') }}"></script>
        @yield("script_content")
	</body>
</html>