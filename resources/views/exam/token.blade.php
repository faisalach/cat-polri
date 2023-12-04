@include('partials/header_user')
<form action="" method="post" id="formToken">
	@csrf
	<div class="container mt-3">
		<div class="card">
			<div class="card-body">
				<div class="form-group">
					<label for="token">Isi Token</label>
					<input type="text" class="form-control" name="token" id="token">
					<small class="text-danger">Token akan diberikan oleh admin.</small>
				</div>
				<div class="form-group">
					<a href="{{ route('exam.list') }}" class="btn btn-light">Kembali</a>
					<button type="submit" id="btnSubmitFormToken" class="btn btn-primary">Submit</button>
					<span class="spinner-border spinner-border-sm text-secondary" id="preloaderFormToken" style="display: none;" role="status">
						<span class="sr-only">Loading...</span>
					</span>
				</div>
			</div>
		</div>
	</div>
</form>
@include('partials/footer_user')
<script src="{{ url('assets') }}/js/jquery.form.min.js"></script>
<script>
	$(function() {
		$("#formToken").ajaxForm({
			beforeSubmit : function() {
				$("#btnSubmitFormToken").prop('disabled',true);
				$("#preloaderFormToken").show();
			},
			dataType : "json",
			success : function(data) {
				$("#btnSubmitFormToken").prop('disabled',false);
				$("#preloaderFormToken").hide();
				notification(data.message,data.status);
				if (data.status == 'success') {
					setTimeout(function() {
						var url_redirect 	= `{{ route('exam.exam',['course_id' => $course_id,'token' => ":token",'course_package_id' => $package_id]) }}`;
						url_redirect 	= url_redirect.replace(":token",data.token);
						location.href = url_redirect;
					},2000)
				}
			},
			error 	: function(error) {
				console.error(error.statusText);
			}
		})
	})
</script>