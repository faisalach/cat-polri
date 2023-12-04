@include('partials.header')
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<div class="row">
	<div class="col-12">
		<div class="card">
			<!-- /.card-header -->
			<div class="card-body">
				<a class="btn btn-primary mb-3" href="{{ route('users.create') }}"><i class="fas fa-fw fa-plus"></i> Buat User</a>
				<a class="d-none" id="refresh_table"></a>
				<table class="table table-bordered table-striped" id="dt_users">
					<thead class="bg-primary">
						<tr>
							<th>Nama</th>
							<th>Username</th>
							<th>Password</th>
							<th>Role</th>
							<th>Kelas</th>
							<th>#</th>
						</tr>
					</thead>
					<tbody></tbody>
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
		$("#dt_users").DataTable({
			processing: true,
			serverSide: true,
			ajax : {
				url : "{{ route('users.get_data') }}"
			},
			columns: [
				{ data : "name" },
				{ data : "username" },
				{ data : "password_ori" },
				{ data : "user_group_name" },
				{ data : "class_name" },
				{ data : null, render : function(data) {
					let update_url 	= '{{ route('users.edit',['user_id' => ':user_id']) }}'.replace(':user_id',data.id);

					let html 	= `
					<div class="dropdown">
						<button class="btn btn-sm dropdown-toggle" type="button"data-toggle="dropdown">
							<i class="fas fa-fw fa-bars"></i>
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item btnEdit" data-id="${data.id}" href="${update_url}"><i class="fas fa-fw fa-edit"></i> Edit</a>
							<a class="dropdown-item btnDelete" data-id="${data.id}" href="#"><i class="fas fa-fw fa-trash"></i> Hapus</a>
						</div>
					</div>
					`;
					return html;
				} },
			],
			order: [[ 0, 'asc' ]]
		});

		$("body").on("click","#refresh_table",function(e) {
			$("#dt_users").DataTable().ajax.reload();
		})
		$("body").on("click",".btnDelete",function(e) {
			e.preventDefault();

			let user_id = $(this).attr('data-id');
			let url 	= '{{ route('users.delete') }}';
			let title 	= "Hapus user";
			let text 	= "Apakah kamu yakin ingin menghapus user ini?";
			let id_refresh 	= "refresh_table";
			let customConfirmButtonText 	= "Hapus";

			confirmation_alert(url, user_id, title, text, id_refresh, customConfirmButtonText);
		})
	})
</script>
