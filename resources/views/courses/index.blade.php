@include('partials.header')
@csrf
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<a href="{{ route('course.create') }}" class="btn btn-primary">Buat Soal Baru</a>
			</div>
		</div>
		<a href="#" class="d-none" id="refresh_page"></a>
		@foreach($courses as $course_type_arr)
		<div class="card">
			<!-- /.card-header -->
			<div class="card-body">
				<h4 class="mb-2">{{ $course_type_arr['name'] }}</h4>
				<hr>
				@foreach($course_type_arr['data'] as $key => $course_category_arr)
				<div class="d-block overflow-auto">
					<h6 class="mb-2">{{ $course_category_arr['name'] }}</h6>
					<div class="custom-control custom-checkbox">
						<input type="checkbox" {{ !empty($course_category_arr['isRandom']) ? 'checked' : '' }} value="{{ $course_category_arr['course_category_id'] }}" class="custom-control-input isRandom" id="customCheck_{{$key}}">
						<label class="custom-control-label" for="customCheck_{{$key}}">Acak Nomor</label>
					</div>
					<table class="table table-bordered table-striped">
					<thead class="bg-primary">
						<tr>
							<th style="width: 50%">Nama Soal</th>
							<th style="width: 30%">Waktu</th>
							<th style="width: 20%">Jumlah Soal</th>
							<th style="width: 20%">#</th>
						</tr>
					</thead>
					<tbody>
						@foreach($course_category_arr['data'] as $course)
						<tr>
							<td style="width: 50%">{{ $course->name }}</td>
							<td style="width: 30%">{{ $course->test_time }} Menit</td>
							<td style="width: 20%">{{ $course->number_of_questions }}</td>
							<td style="width: 20%">
								<div class="dropdown">
									<button class="btn btn-sm dropdown-toggle" type="button"data-toggle="dropdown">
										<i class="fas fa-fw fa-bars"></i>
									</button>
									<div class="dropdown-menu">
										<!-- <a class="dropdown-item btnEdit" href="#"><i class="fas fa-fw fa-edit"></i> Edit</a> -->
										<a class="dropdown-item btnDelete" href="#" data-id="{{ $course->id }}"><i class="fas fa-fw fa-trash"></i> Delete</a>
										<a class="dropdown-item" href="{{ route('question.create',['course_id' => $course->id]) }}"><i class="fas fa-fw fa-pen"></i> Edit Pertanyaan</a>
									</div>
								</div>
							</td>
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
		<!-- /.card -->
	</div>
	<!-- /.col -->
</div>
@include('partials.footer')
<script>
	$(function() {
		$("body").on("change",".isRandom",function(e) {
			let course_category_id = $(this).val();
			let isRandom = $(this).is(":checked") ? 1 : 0;
			$.ajax({
				url : `{{ route('course.update_israndom') }}`,
				data 	: {
					course_category_id : course_category_id,
					isRandom 	: isRandom,
					_token 	: $('{{ csrf_field() }}').val(),
				},
				dataType 	: 'json',
				method 		: 'post',
				success 	: function(data) {
					notification(data.message,data.status);
				}
			})
		})

		$("body").on("click","#refresh_page",function(e) {
			e.preventDefault();
			location.reload();
		});
		$("body").on("click",".btnDelete",function(e) {
			e.preventDefault();

			let course_id = $(this).attr('data-id');
			let url 	= '{{ route('course.delete') }}';
			let title 	= "Hapus soal";
			let text 	= "Apakah kamu yakin ingin menghapus soal ini?";
			let id_refresh 	= "refresh_page";
			let customConfirmButtonText 	= "Hapus";

			confirmation_alert(url, course_id, title, text, id_refresh, customConfirmButtonText);
		})
	})
</script>