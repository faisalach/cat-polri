@include('partials.header')
<form action="" method="post" id="formCreateUser">
	@csrf
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="first_name">Nama Depan</label>
						<input type="text" class="form-control" name="first_name" id="first_name" value="{{ $user_obj->first_name }}">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="last_name">Nama Belakang</label>
						<input type="text" class="form-control" name="last_name" id="last_name" value="{{ $user_obj->username }}">
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label for="username">Username</label>
						<input type="text" class="form-control" disabled name="username" id="username" value="{{ $user_obj->username }}">
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label for="class_id">Kelas</label>
						<select class="form-control" name="class_id" id="class_id">
							@foreach($classes as $class)
							<option value="{{ $class->id }}" {{ $user_obj->class_id == $class->id ? 'selected': "" }}>{{ $class->name }}</option>
							@endforeach
						</select>
					</div>
				</div>


				<div class="col-md-6">
					<div class="form-group">
						<label for="password">Password</label>
						<input type="password" class="form-control" name="password" id="password">
						<small class="text-danger">Isi password jika ingin diubah</small>
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label for="conf_password">Konfirmasi Password</label>
						<input type="password" class="form-control" name="conf_password" id="conf_password">
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<a href="{{ route('users') }}" class="btn btn-light">Kembali</a>
						<button type="submit" id="btnSubmitFormCreateUser" class="btn btn-primary">Submit</button>
						<span class="spinner-border spinner-border-sm text-secondary" id="preloaderFormCreateUser" style="display: none;" role="status">
							<span class="sr-only">Loading...</span>
						</span>
					</div>
				</div>

			</div>
		</div>
	</div>
</form>
@include('partials.footer')
<script src="{{ url('assets') }}/js/jquery.form.min.js"></script>
<script>
	$("#formCreateUser").ajaxForm({
		beforeSubmit : function() {
			$("#btnSubmitFormCreateUser").prop('disabled',true);
			$("#preloaderFormCreateUser").show();
		},
		dataType : "json",
		success : function(data) {
			$("#btnSubmitFormCreateUser").prop('disabled',false);
			$("#preloaderFormCreateUser").hide();
			notification(data.message,data.status);
			if (data.status == 'success') {
				setTimeout(function() {
					var url_redirect 	= `{{ route('users') }}`;
					location.href = url_redirect;
				},2000)
			}
		},
		error 	: function(error) {
			console.error(error.statusText);
		}
	})
</script>