@include('partials.header')
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<div class="row">
	<div class="col-12">
		<div class="card">
			<!-- /.card-header -->
			<div class="card-body">
				<table class="table table-bordered table-striped" id="dt_scores">
					<thead class="bg-primary">
						<tr>
							<th>Nama</th>
							<th>Kelas</th>
							<th>#</th>
						</tr>
					</thead>
					<tbody>
						@foreach($scores as $score_obj)
						<tr>
							<td>{{ $score_obj->name }}</td>
							<td>{{ $score_obj->class_name }}</td>
							<td>
								<div class="dropdown">
									<button class="btn btn-sm dropdown-toggle" type="button"data-toggle="dropdown">
										<i class="fas fa-fw fa-bars"></i>
									</button>
									<div class="dropdown-menu">
										<a class="dropdown-item" href="{{ route('scores',['user_id' => $score_obj->id]) }}"><i class="fas fa-fw fa-star"></i> Score</a>
									</div>
								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<!-- /.card-body -->
		</div>
		<!-- /.card -->
	</div>
	<!-- /.col -->
</div>
@include('partials.footer')
@csrf
<script src="{{ url('adminlte') }}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ url('adminlte') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ url('adminlte') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ url('adminlte') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script>
	$(function() {
		$("#dt_scores").DataTable();
	})
</script>
