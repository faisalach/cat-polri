<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ !empty($title) ? $title . " | " : "" }}Cahaya Logika</title>
	<link rel="shortcut icon" href="{{ url('assets/images/favicon.ico') }}" />
	<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->	
	<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/fontawesome-free/css/all.min.css">
	<link rel="stylesheet" href="{{ url('adminlte') }}/dist/css/adminlte.min.css">
</head>
<body>
	<div class="container pb-5">
		<hr>
		<table class="table table-bordered">
			<tr>
				<th width="500">Nama Lengkap</th>
				<td>{{ strtoupper($user_obj->name) }}</td>
			</tr>
			<tr>
				<th width="500">Kelas</th>
				<td> {{ strtoupper($user_obj->class_name) }}</td>
			</tr>
			<tr>
				<th width="500">Paket Ujian</th>
				<td> {{ strtoupper($course_packages->name) }}</td>
			</tr>
		</table>
		<hr>
		<table class="table table-bordered">
			<thead>
				@if($course_type_id == 1)
				<tr>
					<th width="500">Aspek Penilaian</th>
					<th width="500">Kategori</th>
				</tr>
				@else
				<tr>
					<th width="500">Materi Tes</th>
					<th width="500">Nilai</th>
				</tr>
				@endif
			</thead>
			<tbody>
				@foreach($scores["score"] as $key => $score)
				@if($key != 0 && $course_type_id == 1)
				<tr>
					<td colspan="2"><br></td>
				</tr>
				@endif
				@foreach($score["data"] as $score_data)
				<tr>
					<th width="500">
						{{ $score_data->sub_category_name }}
					</th>
					<td>
						{{ $score_data->result }}
					</td>
				</tr>
				@endforeach
				@endforeach
			</tbody>
			@if(!empty($scores["finish"]))
			<tfoot>
				<tr>
					<th width="500" class="text-center">
						Nilai Akhir
					</th>
					<th>
						{{ $scores["final_score"] }}
					</th>
				</tr>
				@if($course_type_id == 1)
				<tr>
					<th width="500" class="text-center">
						Keterangan
					</th>
					<th>
						{{ $scores["description"] }}
					</th>
				</tr>
				@endif
			</tfoot>
			@endif
		</table>
		<hr>
		@if(session('user_group_id') == 3)
		<a href="{{ route('exam.list') }}" class="btn btn-light">Kembali</a>
		@else
		<a href="{{ route('scores',['user_id' => $user_obj->id]) }}" class="btn btn-light">Kembali</a>
		@endif
		@if(session('user_group_id') != 3)
		<a href="#" onclick="window.print()" class="btn btn-primary">Print</a>
		@endif
	</div>
	<style>
		@media print{
			.btn {display:none;visibility:hidden;}
		}
	</style>
	@if(session('user_group_id') == 3)
	<style>
		@media print{
			body {display:none;visibility:hidden;}
		}
	</style>
	<script>
		document.addEventListener('contextmenu', event => event.preventDefault());
		document.onkeydown = function (e) {
			return false;
		}
	</script>
	@endif
</body>
</html>