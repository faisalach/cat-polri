@include('partials/header_user')
<div class="container mt-3">
	<div class="card">
		<div class="card-body">
			<h5 class="card-title">
				{{ $title }}
			</h5>
			<p>Kamu Sudah mengerjakan soal ini, score kamu : </p>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th width="500">Aspek Penilaian</th>
						<th width="500">Score</th>
					</tr>
				</thead>
				<tbody>
					@foreach($score as $score_data)
					<tr>
						<th width="500">
							{{ $score_data->sub_category_name }}
						</th>
						<td>
							{{ $score_data->result }}
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
				<hr>
			<div class="">
				<a href="{{ route('exam.list') }}" class="btn btn-light">
					<i class="fas fa-fw fa-arrow-left"></i>
					Kembali
				</a>
			</div>
		</div>
	</div>
</div>
@include('partials/footer_user')