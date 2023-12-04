@include('partials/header_login')
<style>
	.login-box{
		max-width: 400px;
		width: 100%;
		margin: 50px auto;
	}
</style>
<div class="login-box">
	<!-- /.login-logo -->
	<div class="card">
		<div class="card-body">
			<div class="card-title mb-3 border-bottom text-center">
				<img src="{{ url('assets/images/logo.png') }}" height="60" class="my-3">
			</div>
			<p class="py-2 text-center">Sign in</p>

			<form action="{{ route('login') }}" method="post" id="formLogin">
				@csrf
				<div class="input-group mb-3">
					<input type="text" name="username" class="form-control" placeholder="Email">
					<div class="input-group-append">
						<div class="input-group-text">
							<span class="fas fa-envelope"></span>
						</div>
					</div>
				</div>
				<div class="input-group mb-3">
					<input type="password" name="password" class="form-control" placeholder="Password">
					<div class="input-group-append">
						<div class="input-group-text">
							<span class="fas fa-lock"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						<!-- <div class="icheck-primary">
							<input type="checkbox" id="remember">
							<label for="remember">
								Remember Me
							</label>
						</div> -->
					</div>
					<!-- /.col -->
					<div class="col-6 text-right">
						<button type="submit" id="btnSubmitFormLogin" class="btn btn-primary">Sign In</button>
						<span class="spinner-border spinner-border-sm text-secondary" id="preloaderFormLogin" style="display: none;" role="status">
							<span class="sr-only">Loading...</span>
						</span>
					</div>
					<!-- /.col -->
				</div>
			</form>
			<!-- <div class="py-3">
				<p class="mb-1">
					<a href="forgot-password.html">I forgot my password</a>
				</p>
				<p class="mb-0">
					<a href="register.html" class="text-center">Register a new membership</a>
				</p>
			</div> -->
		</div>
		<!-- /.login-card-body -->
	</div>
</div>
@include('partials/footer_login')
<script>
	$(function() {
		$("#formLogin").ajaxForm({
			beforeSubmit : function() {
				$("#btnSubmitFormLogin").prop('disabled',true);
				$("#preloaderFormLogin").show();
			},
			dataType : "json",
			success : function(data) {
				$("#btnSubmitFormLogin").prop('disabled',false);
				$("#preloaderFormLogin").hide();
				notification(data.message,data.status);
				if (data.status == 'success') {
					setTimeout(function() {
						location.href = '{{ route('dashboard') }}';
					},1500)
				}
			},
			error 	: function(error) {
				console.error(error.statusText);
			}
		})
	})
</script>
