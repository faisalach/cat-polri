@include('partials.header')
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<form action="" method="post" id="formPackage">
	@csrf
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="form-group">
						<label for="course_package_name">Nama Paket</label>
						<input type="text" class="form-control" name="course_package_name" id="course_package_name" value="{{ !empty($package->name) ? $package->name : '' }}">
					</div>
					<div class="form-group">
						<label for="course_type_id">Tipe Soal</label>
						<select class="form-control" name="course_type_id" id="course_type_id" value="{{ !empty($package->name) ? $package->name : '' }}">
							@foreach($course_types as $course_type)
							<option value="{{ $course_type->id }}" {{ !empty($package->course_type_id) && $course_type->id == $package->course_type_id ? 'selected' : '' }}>{{ $course_type->name }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
			@foreach($courses as $course_type_id => $course_type_arr)
			<div class="card course_type_contain" style="display:none;" data-type_id="{{ $course_type_id }}">
				<!-- /.card-header -->
				<div class="card-body">
					<h4 class="mb-2">{{ $course_type_arr['name'] }}</h4>
					<hr>
					@foreach($course_type_arr['data'] as $course_category_arr)
					<div class="d-block overflow-auto">
						<h6 class="mb-2">{{ $course_category_arr['name'] }}</h6>
						<table class="table table-bordered table-striped">
							<thead class="bg-primary">
								<tr>
									<th style="width: 5%">#</th>
									<th style="width: 50%">Kategori</th>
									<th style="width: 30%">Waktu</th>
									<th style="width: 20%">Jumlah Soal</th>
								</tr>
							</thead>
							<tbody>
								@foreach($course_category_arr['data'] as $course)
								<tr>
									<td style="width: 5%">
										<input type="radio" {{ !empty($course_to_packages[$course->id]) ? "checked" : "" }} name="course_id[{{ $course->course_category_id }}]" value="{{ $course->id }}">
									</td>
									<td style="width: 50%">{{ $course->name }}</td>
									<td style="width: 30%">{{ $course->test_time }} Menit</td>
									<td style="width: 20%">{{ $course->number_of_questions }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					@endforeach

				</div>
				<!-- /.card-body -->
			</div>
			@endforeach
			<div class="card">
				<div class="card-body">
					<a href="{{ route('packages') }}" class="btn btn-light">Kembali</a>
					<button type="submit" id="btnSubmitFormPackage" class="btn btn-primary">Buat Paket</button>
					<span class="spinner-border spinner-border-sm text-secondary" id="preloaderFormPackage" style="display: none;" role="status">
						<span class="sr-only">Loading...</span>
					</span>
				</div>
			</div>
			<!-- /.card -->
		</div>
		<!-- /.col -->
	</div>
</form>
@include('partials.footer')
<script src="{{ url('assets') }}/js/jquery.form.min.js"></script>
<script>
	$(function() {
		$("body").on("click","tr",function(e) {
			$(this).find('input').prop('checked',true);
		})
		$("body").on("change","#course_type_id",function(e) {
			let course_type_id 	= $(this).val();
			showHideCard(course_type_id);	
		})
		function showHideCard(course_type_id) {
			$('.course_type_contain').hide();
			$(".course_type_contain input").prop('disabled',true);
			$(`.course_type_contain[data-type_id=${course_type_id}]`).show();
			$(`.course_type_contain[data-type_id=${course_type_id}] input`).prop('disabled',false);
		}
		$("#course_type_id").trigger('change');

		$("#formPackage").ajaxForm({
			beforeSubmit : function() {
				$("#btnSubmitFormPackage").prop('disabled',true);
				$("#preloaderFormPackage").show();
			},
			dataType : "json",
			success : function(data) {
				$("#btnSubmitFormPackage").prop('disabled',false);
				$("#preloaderFormPackage").hide();
				notification(data.message,data.status);
				if (data.status == 'success') {
					setTimeout(function() {
						var url_redirect 	= `{{ route('packages') }}`;
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