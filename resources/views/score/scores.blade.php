@include('partials/header')
@csrf
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<div class="container mt-3">
	<div class="card">
		<a class="d-none" id="refresh_table"></a>
		<div class="card-body">
			<table class="table table-bordered">
			<tr>
				<th width="500">Nama Lengkap</th>
				<td>{{ strtoupper($user_obj->name) }}</td>
			</tr>
			<tr>
				<th width="500">Kelas</th>
				<td>{{ strtoupper($user_obj->class_name) }}</td>
			</tr>
		</table>
		</div>
	</div>
	<div class="accordion" id="accordionExample">
		@foreach($courses as $course_package_id => $course_packages)
		<div class="card">
			<div class="card-header" id="heading_{{ $course_package_id }}">
				<h2 class="mb-0">
					<button class="btn btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse_{{ $course_package_id }}" aria-expanded="true" aria-controls="collapse_{{ $course_package_id }}">
						{{ $course_packages['name'] }}
					</button>
				</h2>
			</div>

			<div id="collapse_{{ $course_package_id }}" class="collapse show" aria-labelledby="heading_{{ $course_package_id }}" data-parent="#accordionExample">
				@foreach($course_packages['data'] as $course_type_id => $course_obj)
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">
							{{ $course_obj['name'] }}
						</h5>
						<div class="table-responsive">
							<table class="table table-striped table-bordered">
								<thead class="bg-primary text-white">
									<tr>
										<td>Nama Test</td>
										<td>#</td>
									</tr>
								</thead>
								<tbody>
									@foreach($course_obj['data'] as $course)
									<tr>
										<td>{{ $course->course_category_name }}</td>
										<td>
											@if($score_id = isAnswered($course->id,$user_obj->id,$course_package_id))
											<a href="{{ route('scores.detail',['score_id' => $score_id]) }}" class="btn btn-success btnDetail">Nilai</a>
											@if(session('user_group_id') == 1)
											<a href="#" data-id="{{ $score_id }}" class="btn btn-danger btnDelete">Hapus</a>
											@endif
											@else
											Belum Mengerjakan
											@endif
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						@if(isFinishTest($course_type_id,$user_obj->id,$course_package_id))
						<div class="form-group">
							<a href="{{ route('score',['course_type_id' => $course_type_id,'user_id' => $user_obj->id,'course_package_id' => $course_package_id]) }}" target="_blank" class="btn btn-success">Lihat Nilai Akhir</a>
						</div>
						@endif
					</div>
				</div>
				@endforeach
			</div>
		</div>
		@endforeach
	</div>
	<div class="card">
		<div class="card-body">
			<a class="btn btn-light" href="{{ route('scores.list') }}">Kembali</a>
		</div>
	</div>
</div>
@include('partials/footer')
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Nilai </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="modalBodyDetail">
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script>
	$(function() {
		$("body").on("click","#refresh_table",function(e) {
			location.reload();
		})
		$("body").on("click",".btnDelete",function(e) {
			e.preventDefault();

			let score_id = $(this).attr('data-id');
			let url 	= '{{ route('score.delete') }}';
			let title 	= "Hapus Nilai";
			let text 	= "Apakah kamu yakin ingin menghapus nilai ini?";
			let id_refresh 	= "refresh_table";
			let customConfirmButtonText 	= "Hapus";

			confirmation_alert(url, score_id, title, text, id_refresh, customConfirmButtonText);
		})
		$("body").on("click",".btnDetail",function(e) {
			e.preventDefault();
			$("#modalDetail").modal('show');
			$("#modalBodyDetail").empty();
			let url 	= $(this).attr('href');
			// return false;
			$.ajax({
				url 	: url
			}).then(function(content) {
				$("#modalBodyDetail").html(content);
				
			})
		})
	})
</script>