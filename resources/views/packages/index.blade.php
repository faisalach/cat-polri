@include('partials.header')
@csrf
<style>
	.table{
		width: 100% !important;
	}
</style>
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<a href="{{ route('package.edit') }}" class="btn btn-primary">Buat Paket Baru</a>
			</div>
		</div>
		<div class="card">
			<!-- /.card-header -->
			<div class="card-body">
				<div class="table-responsive">
					<a class="d-none" id="refresh_table"></a>
					<table class="table table-bordered table-striped" id="dt_packages">
						<thead class="bg-primary">
							<tr>
								<th style="width: 5px">#</th>
								<th style="width: 100%">Nama Paket</th>
								<th style="width: 20%">#</th>
							</tr>
						</thead>
					</table>
				</div>

			</div>
			<!-- /.card-body -->
		</div>
		<!-- /.card -->
	</div>
	<!-- /.col -->
</div>
@include('partials.footer')
<script src="{{ url('adminlte') }}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ url('adminlte') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ url('adminlte') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ url('adminlte') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script>
	$(function() {
		$("#dt_packages").DataTable({
			processing: true,
			serverSide: true,
			ajax : {
				url : "{{ route('package.get_datas') }}"
			},
			columns: [
				{ data : null, orderable : false, render : function(data) {
					return `<a class="btn btn-sm rounded-circle btn-light btnExpand" href="#"><i class="fas fa-fw fa-arrow-down"></i></a>`;
				} },
				{ data : "name" },
				{ data : null, orderable : false, render : function(data) {
					let update_url 	= '{{ route('package.edit',['package_id' => ':package_id']) }}'.replace(':package_id',data.id);

					let html 	= `
					<div class="dropdown">
						<button class="btn btn-sm dropdown-toggle" type="button"data-toggle="dropdown">
							<i class="fas fa-fw fa-bars"></i>
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="${update_url}"><i class="fas fa-fw fa-pen"></i> Edit Soal</a>
							<a class="dropdown-item btnDelete" data-id="${data.id}" href="#"><i class="fas fa-fw fa-trash"></i> Hapus Paket Soal</a>
						</div>
					</div>
					`;
					return html;
				} },
			],
			order: [[ 1, 'asc' ]]
		});

		$('body').on('click', '#dt_packages .btnExpand', function (e) {
			e.preventDefault();

			let mytable = $("#dt_packages").DataTable();
			let tr = $(this).closest('tr');
			let row = mytable.row( tr );

			if ( row.child.isShown() ) {
				row.child.hide();
				tr.removeClass('shown');
			} else {
				let data 	= row.data();
				row.child( dtChild(data) ).show();
				tr.addClass('shown');
				let url = "{{ route('package.detail_get_datas',['package_id' => ':package_id']) }}";
				url 	= url.replace(':package_id',data.id);
				$("#dt_detail_package_"+data.id).DataTable({
					processing: true,
					serverSide: true,
					ajax : {
						url : url
					},
					columns: [
					{ data : "course_name" },
					{ data : "token" , orderable : false },
					{ data : null, orderable : false, render : function(data) {
						let generate_token_url 	= '{{ route('package.generate_token',['package_id' => ':package_id','course_id' => ':course_id']) }}'
						generate_token_url 		= generate_token_url.replace(':package_id',data.course_package_id);
						generate_token_url 		= generate_token_url.replace(':course_id',data.course_id);
						let html 	= `<a class="btn btn-primary generate_token" data-package_id="${data.course_package_id}" href="${generate_token_url}">Generate Token</a>`;
						return html;
					} },
					],
					order: [[ 0, 'desc' ]]
				});
			}
		});
		function dtChild(data) {
			let div = $("<div>",{class: "card"});
			let html 	= `
			<table class="table table-bordered table-striped" id="dt_detail_package_${data.id}">
				<thead class="bg-primary">
					<tr>
					<th style="width: 50%">Nama Soal</th>
					<th style="width: 30%">Token</th>
					<th style="width: 20%">#</th>
					</tr>
				</thead>
			</table>
			`;

			return html;
		}

		$("body").on("click","#refresh_table",function(e) {
			$("#dt_packages").DataTable().ajax.reload();
		})
		$("body").on("click",".generate_token",function(e) {
			e.preventDefault();
			let url 	= $(this).attr('href');
			let package_id 	= $(this).attr('data-package_id');
			$.ajax({
				url : url,
				data 	: {
					_token 	: $('{{ csrf_field() }}').val(),
				},
				dataType 	: 'json',
				method 		: 'post',
				success 	: function(data) {
					notification(data.message,data.status);
					if (data.status == 'success') {
						$("#dt_detail_package_"+package_id).DataTable().ajax.reload();
					}
				}
			})
		})
		$("body").on("click",".btnDelete",function(e) {
			e.preventDefault();

			let package_id = $(this).attr('data-id');
			let url 	= '{{ route('package.delete') }}';
			let title 	= "Hapus paket";
			let text 	= "Apakah kamu yakin ingin menghapus paket ini?";
			let id_refresh 	= "refresh_table";
			let customConfirmButtonText 	= "Hapus";

			confirmation_alert(url, package_id, title, text, id_refresh, customConfirmButtonText);
		})
	})
</script>