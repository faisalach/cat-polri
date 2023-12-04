<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ !empty($title) ? $title . " | " : "" }}Cahaya Logika</title>
	<link rel="shortcut icon" href="{{ url('assets/images/favicon.ico') }}" />
	<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->
	<link rel="stylesheet" href="{{ url('assets') }}/css/jquery.jgrowl.min.css"/>
	
	<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/fontawesome-free/css/all.min.css">
	
	<link rel="stylesheet" href="{{ url('adminlte') }}/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
	<div class="wrapper">
		
		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
				</li>
			</ul>
			
			<ul class="navbar-nav ml-auto">
				<li class="nav-item">
					<a class="nav-link" href="{{ route('logout') }}" role="button">
						<i class="fas fa-sign-out-alt"></i>
					</a>
				</li>
			</ul>
		</nav>
		
		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			
			<a href="{{ route('dashboard') }}" class="brand-link">
				<img src="{{ url('assets/images/logo-only.png') }}" alt="Logo" class="brand-image img-circle" style="opacity: .8">
				<span class="brand-text font-weight-light">Cahaya Logika</span>
			</a>

			
			<div class="sidebar">
				
				<div class="user-panel mt-3 pb-3 mb-3 d-flex">
					<div class="image">
						<!-- <img src="{{ url('adminlte') }}/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image"> -->
					</div>
					<div class="info">
						<a href="#" class="d-block">{{ session("name") }}</a>
					</div>
				</div>
				
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
						<li class="nav-item">
							<a href="{{ route('dashboard') }}" class="nav-link {{ !empty($nav_active) && $nav_active == 'dashboard' ? 'active' : '' }}">
								<i class="nav-icon fas fa-home"></i>
								<p>
									Dashboard
								</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ route('courses') }}" class="nav-link {{ !empty($nav_active) && $nav_active == 'courses' ? 'active' : '' }}">
								<i class="nav-icon fas fa-pen"></i>
								<p>
									Soal Tes
								</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ route('packages') }}" class="nav-link {{ !empty($nav_active) && $nav_active == 'packages' ? 'active' : '' }}">
								<i class="nav-icon fas fa-archive"></i>
								<p>
									Paket Ujian
								</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ route('scores.list') }}" class="nav-link {{ !empty($nav_active) && $nav_active == 'scores' ? 'active' : '' }}">
								<i class="nav-icon fas fa-star"></i>
								<p>
									Hasil Tes
								</p>
							</a>
						</li>
						@if(session('user_group_id') == 1)
						<li class="nav-item">
							<a href="{{ route('users') }}" class="nav-link {{ !empty($nav_active) && $nav_active == 'users' ? 'active' : '' }}">
								<i class="nav-icon fas fa-users"></i>
								<p>
									User
								</p>
							</a>
						</li>
						@endif
					</ul>
				</nav>

			</div>

		</aside>


		<div class="content-wrapper">

			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1>{{ $title }}</h1>
						</div>
						<!-- <div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active">User Profile</li>
							</ol>
						</div> -->
					</div>
				</div>
			</section>


			<section class="content">
				<div class="container-fluid">
					

					


