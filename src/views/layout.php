<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
	<meta charset="utf-8">
	<title>Vessel</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">

		<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<!-- <link rel="shortcut icon" href="/bootstrap/img/favicon.ico">
		<link rel="apple-touch-icon" href="/bootstrap/img/apple-touch-icon.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/bootstrap/img/apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/bootstrap/img/apple-touch-icon-114x114.png"> -->

		<style type="text/css">

			header {
				margin-bottom:30px;
			}

		</style>

</head>
<body>

	<script type="text/x-handlebars">

		<header class="navbar navbar-default navbar-static-top" role="banner">
			<div class="container">
				<div class="navbar-header">
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a href="/" class="navbar-brand">Vessel</a>
				</div>
				<nav class="collapse navbar-collapse" role="navigation">
					<ul class="nav navbar-nav">
						<li>{{#linkTo 'pages' activeClass="selected"}}Pages{{/linkTo}}</li>
						<li><a href="#">Blocks</a></li>
						<li><a href="#">Media</a></li>
						<li><a href="#">Users</a></li>
						<li><a href="#">Settings</a></li>
					</ul>
				</nav>
			</div>
		</header>
		
		<!-- Begin Body -->
		<div class="container">
			<div class="row">
				<div class="col-md-3" id="leftCol">

					<div class="well"> 
						<ul class="nav nav-stacked" id="sidebar">
							<li><a href="#sec1">Section 1</a></li>
							<li><a href="#sec2">Section 2</a></li>
							<li><a href="#sec3">Section 3</a></li>
							<li><a href="#sec4">Section 4</a></li>
						</ul>
					</div>

				</div>
				<div class="col-md-9">
					<h2 id="sec0">Title</h2>
					
					{{outlet}}

				</div> 
			</div>
		</div>
	</script>

	<script type="text/x-handlebars" id="pages">

		Hi, this is the pages route.

		<table class="table table-bordered">

			<thead>
				<tr>
					<th>Id</th>
					<th>Title</th>
					<th>Completed</th>
				</tr>
			</thead>

			<tbody>

				{{#each}}

				<tr>
					<td>{{id}}</td>
					<td>{{title}}</td>
					<td>{{isCompleted}}</td>
				</tr>

				{{/each}}

			</tbody>

		</table>

	</script>

	<script type='text/javascript' src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/handlebars.js/1.1.2/handlebars.min.js"></script>
	<script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/ember.js/1.4.0/ember.min.js"></script>
	<script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/ember-data.js/1.0.0-beta.6/ember-data.min.js"></script>

	<script type='text/javascript' src="<?php echo URL::asset('packages/hokeo/vessel/js/app.js') ?>"></script>

	<script type='text/javascript'>

	$(document).ready(function() {

	});

	</script>

	<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', 'UA-40413119-1', 'bootply.com');
	ga('send', 'pageview');
	</script>
</body>
</html>