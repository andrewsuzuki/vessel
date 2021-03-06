<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
	<meta charset="utf-8">
	<title>Vessel</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	{{ $css }}

	<!--[if lt IE 9]>
	<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- <link rel="shortcut icon" href="/bootstrap/img/favicon.ico">
	<link rel="apple-touch-icon" href="/bootstrap/img/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/bootstrap/img/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/bootstrap/img/apple-touch-icon-114x114.png"> -->

	<style type="text/css">

		header {
			margin-bottom: 30px;
		}

		footer {
			margin: 25px 0 15px;
		}

		.heading-top {
			margin: 0 0 15px 0;
		}

		.mb-15 {
			margin-bottom: 15px;
		}

		.input-monospaced {
			font-family: 'Courier New', Courier, Monospace;
		}

		.label-pagehistory {
			font-size: 55%;
			vertical-align: 5px;
		}

	</style>

</head>
<body>

	<header class="navbar navbar-default navbar-static-top" role="banner">
		<div class="container">
			<div class="navbar-header">
				<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">{{ t('layout.toggle-nav') }}</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				{{ link_to_route('vessel', 'Vessel', null, array('class' => 'navbar-brand')) }}

			</div>
			<nav class="collapse navbar-collapse" role="navigation">
				{{ $mainmenu }}
				
				<ul class="nav navbar-nav navbar-right">
					<li><a href="{{ Config::get('vset::site.url') }}">{{ t('layout.menu.view-site') }}</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ Auth::user()->username }} <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li>{{ link_to_route('vessel.me', t('layout.menu.user-settings')) }}</li>
							<li>{{ link_to_route('vessel.logout', t('general.logout')) }}</li>
						</ul>
					</li>
				</ul>
			</nav>
		</div>
	</header>
	
	<!-- Begin Body -->
	<div class="container">
		<div class="row">
			<!-- <div class="col-md-3" id="leftCol">

				<div class="well"> 
					<ul class="nav nav-stacked" id="sidebar">
						<li><a href="#sec1">Section 1</a></li>
						<li><a href="#sec2">Section 2</a></li>
						<li><a href="#sec3">Section 3</a></li>
						<li><a href="#sec4">Section 4</a></li>
					</ul>
				</div>

			</div> -->
			<div class="col-lg-12">
				<div id="vessel-notifications">
					{{ $notifications }}
					
				</div>

				{{ fire('back.page-top') }}

				{{ isset($title) ? '<h2 id="vessel-page-title" class="heading-top">'.$title.'</h2>' : '' }}

				{{ fire('back.content-top') }}

				@yield('content')

				{{ fire('back.content-bottom') }}

				{{ fire('back.page-bottom') }}

			</div> 
		</div>
	</div>

	<footer>
		<div class="container">
			<p><a href="//vesselcms.com">Vessel</a> v{{ Hokeo\Vessel\Facades\Vessel::getVersion('full') }} &nbsp;&middot;&nbsp; <a href="//github.com/hokeo/vessel">GitHub</a> &nbsp;&middot;&nbsp; <a href="//vesselcms.com/support">{{ t('general.support') }}</a></p>
		</div>
	</footer>
	
	<script id="vessel-alert-template" type="text/x-handlebars-template">

	<div class="modal fade" id="vessel-alert" tabindex="-1" role="dialog" aria-labelledby="vessel-alert-label" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="vessel-alert-label">@{{title}}</h4>
				</div>
				<div class="modal-body">
					@{{body}}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ t('general.close') }}</button>
				</div>
			</div>
		</div>
	</div>

	</script>
	
	<!-- start other handlebars templates -->

	@yield('templates')

	<!-- end other handlebars templates -->
	
	{{ fire('back.scripts-pre') }}
	
	<script> var base_site_url = '{{ URL::to('/') }}'; var base_vessel_url = '{{ URL::route('vessel') }}'; </script>

	{{ fire('back.scripts-assets-pre') }}
	{{ $js }}

	{{ fire('back.scripts-assets-post') }}

	<script type='text/javascript' src="{{ Hokeo\VEssel\Facades\Asset::getUrlFromNamespace('Hokeo/Vessel', 'js/app.js') }}"></script>

	{{ fire('back.scripts-post') }}

</body>
</html>