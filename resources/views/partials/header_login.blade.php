<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ !empty($title) ? $title . " | " : "" }}Cahaya Logika</title>
	<link rel="shortcut icon" href="{{ url('assets/images/favicon.ico') }}" />
	
	<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->
	
	<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/fontawesome-free/css/all.min.css">
	<link rel="stylesheet" href="{{ url('assets') }}/css/bootstrap.min.css">
	<link rel="stylesheet" href="{{ url('assets') }}/css/jquery.jgrowl.min.css"/>
</head>
<body class="bg-light">
	<nav class="navbar navbar-expand bg-dark navbar-dark">
		<div class="container">
			<a class="navbar-brand" href="#">
				<img src="{{ url('assets/images/logo.png') }}" height="60" class="d-inline-block align-top" alt="">
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse " id="navbarNav">
				<ul class="navbar-nav ml-md-auto">
					<li class="nav-item {{ !empty($nav_active) && $nav_active == 'login' ? 'active' : '' }}">
						<a class="nav-link" href="{{ route('login') }}">Login</a>
					</li>
					<!-- <li class="nav-item">
						<a class="nav-link" href="#">Register</a>
					</li> -->
				</ul>
			</div>
		</div>
	</nav>





