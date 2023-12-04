@include('partials.header')
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{ url('adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<div class="row">
	<div class="col-12 bg-white p-4">
		<form action="{{ route('course.create') }}" method="post" id="formCreateCourse">
			@csrf
			<div class="form-group">
				<label for="course_type_id">Jenis Tes</label>
				<select class="form-control" id="course_type_id" name="course_type_id">
					@foreach($course_categories as $type_id => $type)
					<option value="{{ $type_id }}">{{ $type['name'] }}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group">
				<label for="course_category_id">Nama Tes</label>
				<select class="form-control" id="course_category_id" name="course_category_id">
					@foreach($course_categories[key($course_categories)]['data'] as $category)
					<option value="{{ $category->id }}">{{ $category->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="">
				<a href="{{ route('courses') }}" class="btn btn-light">Kembali</a>
				<button type="submit" id="btnSubmitFormCreateCourse" class="btn btn-primary">Buat Soal</button>
				<span class="spinner-border spinner-border-sm text-secondary" id="preloaderFormCreateCourse" style="display: none;" role="status">
					<span class="sr-only">Loading...</span>
				</span>
			</div>
		</form>
		<!-- /.card -->
	</div>
	<!-- /.col -->
</div>
@include('partials.footer')
<script src="{{ url('assets') }}/js/jquery.form.min.js"></script>

<script>
	$(function() {
		$('body').on('change','#course_type_id',function() {
			let type_id 	= $(this).val();
			let categories 	= {!! json_encode($course_categories) !!};
			let data 		= categories[type_id]['data'];
			$("#course_category_id").empty();
			$.each(data,function(k,category) {
				$("<option>",{value:category.id}).html(category.name).appendTo('#course_category_id');
			})
		})
		$("#formCreateCourse").ajaxForm({
			beforeSubmit : function() {
				$("#btnSubmitFormCreateCourse").prop('disabled',true);
				$("#preloaderFormCreateCourse").show();
			},
			dataType : "json",
			success : function(data) {
				$("#btnSubmitFormCreateCourse").prop('disabled',false);
				$("#preloaderFormCreateCourse").hide();
				notification(data.message,data.status);
				if (data.status == 'success') {
					setTimeout(function() {
						var url_redirect 	= `{{ route('question.create',['course_id' => ":course_id"]) }}`;
						url_redirect 	= url_redirect.replace(':course_id',data.course_id);
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