@include('partials.header')
<style>
	#number_box{
		display: flex;
		flex-wrap: wrap;
	}
	#number_box .number{
		flex: 0 0 40px;
		width: 40px;
		height: 40px;
		margin: 3px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 3px;
	}
	#number_box .number:hover{
		background-color: #eaeaea;
		color: black;
	}

	#number_box .number.active{
		background-color: #888;
		color: white;
	}
	#questions_box .card{
		display: none;
	}

	#questions_box .card.active{
		display: block;
		opacity: 1;
	}
	.box_file{
		display: flex;
		flex-wrap: wrap;
	}
	.box_file .item_file .close{
		position: absolute;
		right: 0;
		top: 0;
	}

	.box_file .item_file .close{
		background-color: red;
		opacity: 1;
		
	}
	.box_file .item_file{
		position: relative;
		flex: 0 0 200px;
		margin-right: 30px;
		margin-top: 10px;
		border: 1px solid #eaeaea;
		padding: 5px;
	}
	.box_file .item_file img{
		width: 100%;
	}
</style>
<form action="{{ !empty($questions) ? route('question.update',['course_id' => $course->id]) : route('question.create',['course_id' => $course->id]) }}" method="post" id="formQuestion" enctype="multipart/form-data">
	@csrf
	@if(empty(count_course($course->course_category_id)))
	<div class="container-fluid">
		<div class="card">
			<div class="card-body ">
				<div class="row">
					<div class="col-md-6">
						<div class="d-flex align-items-center">
							<label class="m-0 mr-3">Jumlah Soal</label>
							<input type="number" name="number_of_questions" id="numberQuestion" class="form-control d-inline-block text-center mr-2" min="0" style="width: 70px;" placeholder="0">
							<button class="btn btn-primary" id="setNumber" type="button">Set</button>
						</div>
					</div>
					<div class="col-md-6">
						<label class="m-0 mr-3">Waktu Pengerjaan Soal</label>
						<input type="number" name="test_time" id="testTime" class="form-control d-inline-block text-center mr-2" min="0" style="width: 80px;" placeholder="Menit">
						<label class="m-0 mr-3">Menit</label>
					</div>
				</div>
			</div>
		</div>
	</div>
	@else
	<input type="hidden" name="number_of_questions" value="{{ $course->number_of_questions }}">
	<input type="hidden" name="test_time" value="{{ $course->test_time }}">
	@endif
	<div class="container-fluid" id="number_container" style="display:none;">
		<div class="card">
			<div class="card-body">
				<strong class="mr-3">Nomor Soal</strong>
				<div id="number_box"></div>
			</div>
		</div>
	</div>
	<div class="container-fluid" id="questions_box" style="display:none;"></div>
</form>

@include('partials.footer')
<script src="{{ url('assets') }}/js/jquery.form.min.js"></script>

<link rel="stylesheet" href="{{ url('assets') }}/css/froala_editor.pkgd.min.css"/>
<link rel="stylesheet" href="{{ url('assets') }}/css/froala_style.min.css"/>
<script src="{{ url('assets') }}/js/froala_editor.pkgd.min.js"></script>

<script>
	$(function(){

		let data 	= [];
		let editor;
		@if(!empty($course->number_of_questions) && !empty($questions))

		$("#number_container").show();
		$("#questions_box").show();
		$("#number_box").empty();
		$("#questions_box").empty();

		data 	= {!! json_encode($questions) !!};

		htmlNumber({{ $course->number_of_questions }});
		htmlQuestion({{ $course->number_of_questions }},data);
		htmlSave();
		$("#numberQuestion").val({{ $course->number_of_questions }});
		$("#testTime").val({{ $course->test_time }});

		@elseif(!empty(count_course($course->course_category_id)))

		$("#number_container").show();
		$("#questions_box").show();
		$("#number_box").empty();
		$("#questions_box").empty();

		htmlNumber({{ count_course($course->course_category_id) }});
		htmlQuestion({{ count_course($course->course_category_id) }});
		htmlSave();
		@endif

		$("body").on("click","#setNumber",function(e) {
			let value 	= $('#numberQuestion').val();
			count 		= parseInt(value);
			if (count > 0) {
				$("#number_container").show();
				$("#questions_box").show();
				$("#number_box").empty();
				$("#questions_box").empty();

				htmlNumber(count);
				htmlQuestion(count);
				htmlSave();
			}
		});

		$("body").on("click",".box_file .item_file .close",function() {
			$(this).closest('.item_file')[0].outerHTML = '';
		})

		$("body").on("click","#number_box .number",function(e) {
			let index 	= $("#number_box .number").index($(this));
			let index_active 	= $("#number_box .number").index($("#number_box .number.active"));
			if (index == index_active) {
				return false;
			}
			$("#number_box .number").removeClass("active");
			$("#questions_box .card").removeClass("active");

			$($("#number_box .number")[index]).addClass("active");
			setTimeout(function() {
				$($("#questions_box .card")[index]).addClass("active");
				initFroala();
			},300);
		});

		$("body").on("click",".button_box_save",function(e) {
			let html_table_result 	= ``;

			@if(!empty(get_course_sub_categpry($course->course_category_id)))
			let sub_category 	= JSON.parse(`{!! json_encode(get_course_sub_categpry($course->course_category_id)) !!}`);
			let sub_category_arr 	= [];
			let sub_category_name 	= [];
			for (let i = 0; i < sub_category.length; i++) {
				let id 	= sub_category[i].id;
				sub_category_arr[id] = 0;
				sub_category_name[id] = sub_category[i].name;
			}

			for(let i =0; i < $(".select_sub_category_id").length; i++){
				let id 	= $($(".select_sub_category_id")[i]).val();
				sub_category_arr[id] += 1;
			}

			$.each(sub_category_arr,function(k,val) {
				if(sub_category_arr[k] == undefined){
					return true;
				}
				html_table_result 	+= `
				<tr>
				<td class="pl-0 pr-3 py-3" width="300">Aspek ${sub_category_name[k]}</td>
				<td class="p-3">: ${sub_category_arr[k]}</td>
				</tr>
				`;
			})
			html_table_result += `
			<tr class="border-top">
				<td class="pl-0 pr-3 py-3" width="300">Jumlah Soal</td>
				<td class="p-3">: ${$("#questions_box .card:not(.box_save)").length}</td>
			</tr>
			`;
			@else
			html_table_result += `
			<tr>
				<td class="pl-0 pr-3 py-3" width="300">Jumlah Soal</td>
				<td class="p-3">: ${$("#questions_box .card:not(.box_save)").length}</td>
			</tr>
			`;
			
			@endif



			$("#table_result").html(html_table_result);
		});

		function htmlNumber(count) {
			let html 	= '';
			for(let i = 0; i < count;i++){
				html 	= `<span class="number border ${ i == 0 ? 'active' : '' }">${ i + 1 }</span>`;
				$("#number_box").append(html);
			}
		}
		function htmlQuestion(count) {
			let html 	= '';
			for(let i = 0; i < count;i++){
				html 	= `
				<div class="card ${ i == 0 ? 'active' : '' }" id="card_${i}">
					<div class="card-body">
						<strong class="mr-3">${ i + 1 }.</strong>
						@if(!empty(get_course_sub_categpry($course->course_category_id)))
						<div class="form-group">
							<p class="mb-2">Aspek</p>
							<select name="sub_category_ids[${i}]" class="form-control select_sub_category_id">
								@foreach(get_course_sub_categpry($course->course_category_id) as $course_sub_category_obj)
								<option ${data[i] != undefined && data[i].course_sub_category_id == '{{ $course_sub_category_obj->id }}' ? 'selected' : '' } value="{{ $course_sub_category_obj->id }}">{{$course_sub_category_obj->name}}</option>
								@endforeach
							</select>
						</div>
						@endif
						<div class="form-group">
							<p class="m-0">Pertanyaan</p>
							<small class="mb-2 d-block text-danger">Dapat berupa gambar atau text atau keduanya</small>
							<textarea class="form-control input_questions" id="questions_${i}" name="questions[${i}]" placeholder="Masukkan Pertanyaan">${data[i] != undefined ? data[i].question : '' }</textarea>
						</div>
						<div class="form-group">
							<p class="m-0">Opsi</p>
							<small class="mb-2 d-block text-danger">Tandai Jawaban yang benar</small>
							@foreach(choice_option() as $key => $val)
							<div class="d-flex justify-content-center align-items-center form-group">
								<input name="correct_choices[${i}]" ${data[i] != undefined && data[i].correct_choice == '{{ $val }}' ? 'checked' : ''  } value="{{ $val }}" type="radio" class="mr-3">
								<strong class="mr-3">{{ $val }}.</strong>
								<div class="w-100">
									<input class="form-control d-inline-block" type="text" name="choices[${i}][{{$val}}]" value="${data[i] != undefined && data[i].choices['{{ $val }}'] != undefined ? data[i].choices['{{ $val }}'] : ''  }" placeholder="Masukkan Jawaban Opsi {{$val}}">
								</div>
							</div>
							@endforeach
						</div>
					</div>
				</div>
				`;
				$("#questions_box").append(html);
			}
			initFroala();
		}

		function initFroala() {
			if (editor) {
				if (Array.isArray(editor)) {
					$.each(editor,function(key,el) {
						el.destroy();
					})
				}else{
					editor.destroy();
				}
			}
			let active 	= $("#number_box .number.active:visible");
			let index 	= $("#number_box .number").index(active);
			let element_id 	= `#questions_${index}`;
			editor 	= new FroalaEditor(element_id, {
				events: {
					"image.beforeUpload": function(files) {
						var editor = this;
						if (files.length) {
						// Create a File Reader.
						var reader = new FileReader();
						// Set the reader to insert images when they are loaded.
						reader.onload = function(e) {
							var result = e.target.result;
							editor.image.insert(result, null, null, editor.image.get());
						};
						// Read image as base64.
						reader.readAsDataURL(files[0]);
					}
					editor.popups.hideAll();
						// Stop default upload chain.
						return false;
					}
				},
				methods : function() {
					editor.image.align('left');
				}
			})
		}

		function htmlSave() {
			let html_number 	= `<span class="number border button_box_save" style="flex : 0 0 70px">Simpan</span>`;
			$("#number_box").append(html_number);

			let html_box 		= `
				<div class="card box_save">
					<div class="card-body">
						<table class="w-100 mb-3" id="table_result"></table>
						<a href="{{ route('courses') }}" class="btn btn-light"><i class="fas fa-fw fa-arrow-left"></i> Kembali</a>
						<button class="btn btn-primary" type="submit" id="btnSubmitFormQuestion"><i class="fas fa-fw fa-save"></i> Simpan</button>
						<span class="spinner-border spinner-border-sm text-secondary" id="preloaderFormQuestion" style="display: none;" role="status">
							<span class="sr-only">Loading...</span>
						</span>
					</div>
				</div>
			`;
			$("#questions_box").append(html_box);
		}

		$("#formQuestion").ajaxForm({
			beforeSubmit : function() {
				$("#btnSubmitFormQuestion").prop('disabled',true);
				$("#preloaderFormQuestion").show();
			},
			dataType : "json",
			success : function(data) {
				$("#btnSubmitFormQuestion").prop('disabled',false);
				$("#preloaderFormQuestion").hide();
				notification(data.message,data.status);
				if (data.status == 'success') {
					@if(!empty($questions))
					let newAction 	= "{{ route('question.update',['course_id' => $course->id]) }}";
					$("#formQuestion").attr('action',newAction);
					@endif
				}
			},
			error 	: function(error) {
				console.error(error.statusText);
			}
		})
	})
</script>
