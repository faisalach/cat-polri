@include('partials/header_user')
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<div class="container mt-3">
	<div class="accordion" id="accordionExample">
		@foreach($courses as $course_package_id => $course_package_arr)
		<div class="card mb-2 border">
			<div class="card-header bg-white" id="heading_{{ $course_package_id }}">
				<h2 class="mb-0">
					<button class="btn btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse_{{ $course_package_id }}" aria-expanded="true" aria-controls="collapse_{{ $course_package_id }}">
						<span class="font-weight-bold " style="color:#ff6000">{{ $course_package_arr['name'] }}</span>
					</button>
				</h2>
			</div>

			<div id="collapse_{{ $course_package_id }}" class="collapse show" aria-labelledby="heading_{{ $course_package_id }}" data-parent="#accordionExample">
				@foreach($course_package_arr['data'] as $course_type_id => $course_obj)
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
										<td>Waktu Test</td>
										<td>Jumlah Soal</td>
										<td>#</td>
									</tr>
								</thead>
								<tbody>
									@foreach($course_obj['data'] as $course)
									<tr>
										<td>{{ $course->course_category_name }}</td>
										<td>{{ $course->test_time }} menit {{ $course->course_category_id == 3 ? '/ Kolom' :'' }}</td>
										<td>{{ $course->course_category_id == 3 ? '10 Kolom, 50 Soal / Kolom' : $course->number_of_questions . " Soal " }}</td>
										<td>
											@if(isAnswered($course->id,$user_obj->id,$course_package_id))
											<a href="{{ route('exam.exam',['course_id' => $course->id,'course_package_id' => $course_package_id]) }}" class="btn btn-success">Nilai</a>
											@else
											<a href="{{ route('exam.exam',['course_id' => $course->id,'course_package_id' => $course_package_id]) }}" class="btn btn-primary">Kerjakan</a>
											@endif
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						@if(isFinishTest($course_type_id,session('user_id'),$course_package_id))
						<div class="form-group">
							<a href="{{ route('score',['course_type_id' => $course_type_id,'course_package_id' => $course_package_id]) }}" target="_blank" class="btn btn-success">Lihat Nilai Akhir</a>
						</div>
						@endif
					</div>
				</div>
				@endforeach
			</div>
		</div>
		@endforeach
	</div>
	
</div>
@include('partials/footer_user')