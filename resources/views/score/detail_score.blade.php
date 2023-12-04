
<h5>
	{{ $title }}
</h5>
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

