<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
	<meta charset="utf-8">
	<title>Vessel</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">

		<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<!-- <link rel="shortcut icon" href="/bootstrap/img/favicon.ico">
		<link rel="apple-touch-icon" href="/bootstrap/img/apple-touch-icon.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/bootstrap/img/apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/bootstrap/img/apple-touch-icon-114x114.png"> -->
	
	<style type="text/css">

		body {
			padding-top: 20px;
		}

	</style>
</head>
<body>
	
	<div class="container">
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				
				{{ \Krucas\Notification\Facades\Notification::showAll() }}

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><a href="http://vesselcms.com" target="_blank">{{ $title }}</a></h3>
					</div>
					<div class="panel-body">
						
						@yield('content')

					</div>
				</div>
			</div>
		</div>
	</div>

</body>
</html>