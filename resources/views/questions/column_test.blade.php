@include('partials.header')
<style>
	#number_box .number_box_contain{
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
</style>
<div class="container-fluid" id="number_container">
	<div class="card">
		<div class="card-body">
			<div class="row align-item-center mb-3">
				<div class="col-md-6">
					<label class="">Nomor Soal</label>
				</div>
				<div class="col-md-6 text-md-right">
					<a href="{{ route('courses') }}" class="btn btn-light"><i class="fas fa-fw fa-arrow-left"></i> Kembali</a>
					<button class="btn btn-primary" type="button" id="btnSubmitFormQuestion"><i class="fas fa-fw fa-save"></i> Simpan</button>
					<span class="spinner-border spinner-border-sm text-secondary" id="preloaderFormQuestion" style="display: none;" role="status">
						<span class="sr-only">Loading...</span>
					</span>
				</div>
			</div>
			<div id="number_box">
				<ul class="nav nav-tabs" id="myTab" role="tablist">
					@for($i = 0; $i < count_course(3)[0]; $i++)
					<li class="nav-item" role="presentation">
						<a class="nav-link {{ $i == 0 ? 'active' : '' }}" id="column_{{ $i }}-tab" data-toggle="tab" href="#column_{{ $i }}" role="tab" aria-controls="column_{{ $i }}" aria-selected="true">Kolom {{ $i+1 }}</a>
					</li>
					@endfor
				</ul>
				<div class="tab-content" id="myTabContent">
					@for($i = 0; $i < count_course(3)[0]; $i++)
					<div class="tab-pane fade show {{ $i == 0 ? 'active' : '' }}" id="column_{{ $i }}" role="tabpanel" aria-labelledby="column_{{ $i }}-tab">
						<div class="number_box_contain">
							@for($j = 0; $j < count_course(3)[1]; $j++)
							<span data-column="{{ $i }}" data-row="{{ $j }}" class="number border {{ $j == 0 ? 'active' : '' }}">{{ $j + 1 }}</span>
							@endfor
						</div>
					</div>
					@endfor
				</div>

			</div>
		</div>
	</div>
</div>
<div class="container-fluid" id="questions_box">
	@for($i = 0; $i < count_course(3)[0]; $i++)
	<form action="{{ !empty($questions) ? route('question.update_column',['course_id' => $course->id]) : route('question.create_column',['course_id' => $course->id]) }}" id="column_box_{{$i}}" method="post">
		@csrf
		@for($j = 0; $j < count_course(3)[1]; $j++)
		@php
		$question_obj 	= !empty($questions[$i][$j]) ? $questions[$i][$j] : [];
		@endphp
		<div class="card {{ $i == 0 && $j == 0 ? 'active' : '' }}" id="card_{{$i}}_{{$j}}">
			<div class="card-body">
				<!-- <label>Kolom ke-{{$i+1}} Soal ke-{{$j+1}}</label> -->
				<div class="form-group">
					<p class="m-0">Pertanyaan</p>
					<small class="mb-2 d-block text-danger">Dapat berupa gambar atau text atau keduanya</small>
					<textarea class="form-control input-question input_questions-{{$i}}" id="questions_{{ $i }}_{{ $j }}" name="questions[{{ $i }}][{{ $j }}]" placeholder="Masukkan Pertanyaan">{{ !empty($question_obj->question) ? $question_obj->question : '' }}</textarea>
				</div>
				<div class="form-group">
					<p class="m-0">Opsi</p>
					<small class="mb-2 d-block text-danger">Tandai Jawaban yang benar</small>
					@foreach(choice_option($course->id) as $key => $val)
					<label class="d-flex justify-content-start align-items-center form-group">
						<input name="correct_choices[{{ $i }}][{{ $j }}]" {{ !empty($question_obj->correct_choice) && $question_obj->correct_choice == $val ? 'checked' : '' }} value="{{ $val }}" type="radio" class="mr-3 input-correct_choice">
						<strong class="mr-3">{{ $val }}.</strong>
						<div class="w-100">
							<input type="hidden" class="form-control d-inline-block  input-choice" name="choices[{{ $i }}][{{ $j }}][{{ $val }}]" value="{{ !empty($question_obj->choices[$val]) ? $question_obj->choices[$val] : '' }}" placeholder="Masukkan Jawaban Opsi {{$val}}">
						</div>
					</label>
					@endforeach
				</div>
			</div>
		</div>
		@endfor
	</form>
	@endfor
</div>
@include('partials.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>

<link rel="stylesheet" href="{{ url('assets') }}/css/froala_editor.pkgd.min.css"/>
<link rel="stylesheet" href="{{ url('assets') }}/css/froala_style.min.css"/>
<script src="{{ url('assets') }}/js/froala_editor.pkgd.min.js"></script>
<script>
	$(function() {
		let editor;
		initFroala();
		

		$("body").on("click","#number_box .number",function(e) {

			let column 	= $(this).attr('data-column');
			let row 	= $(this).attr('data-row');

			let column_active 	= $("#myTabContent .tab-pane").index($($("#myTabContent .tab-pane.active")))
			column_active 		= parseInt(column_active);
			let number_active 	= $($("#number_box .number_box_contain")[column_active]).find(".number.active");
			let row_active 		= number_active.attr('data-row');
			if (column == column_active && row == row_active) {
				return false;
			}
			$(`#number_box .number[data-column=${column}]`).removeClass("active");
			$("#questions_box .card").removeClass("active");
			$(this).addClass("active");
			setTimeout(function() {
				$(`#card_${column}_${row}`).addClass("active");
				initFroala();
			},300);
		});

		$("body").on("click","#number_box .nav-link",function(e) {
			let index 	= $("#number_box .nav-link").index($(this));
			$(`#column_${index}`).find(".number.active").click();
		});


		$("body").on("click","#btnSubmitFormQuestion",function(e) {
			e.preventDefault();
			$("#btnSubmitFormQuestion").prop('disabled',true);
			$("#preloaderFormQuestion").show();

			let column 	= parseInt('{{ count_course(3)[0] }}');
			// column 	= 1;
			for (let i = 0; i < column; i++) {
				let column_box 	= $(`#column_box_${i}`);
				let form_data 	= {};
				form_data._token 	= $("[name=_token]").val();

				$.each(column_box.find('.input-choice'),function(k,el) {
					let name 	= $(el).attr('name');
					let value 	= $(el).val();
					form_data[name] = value;
				});

				$.each(column_box.find('.input-question'),function(k,el) {
					let name 	= $(el).attr('name');
					let value 	= $(el).val();
					form_data[name] = value;
				});

				$.each(column_box.find('.input-correct_choice:checked'),function(k,el) {
					let name 	= $(el).attr('name');
					let value 	= $(el).val();
					form_data[name] = value;
				});
				let success = false;
				$.ajax({
					// async 	: false,
					url 	: column_box.attr('action'),
					method 	: column_box.attr('method'),
					data 	: form_data,
					dataType : "json",
					success : function(data) {
						notification(data.message,data.status);
						if (data.status == 'success') {
							@if(!empty($questions))
							let newAction 	= "{{ route('question.update_column',['course_id' => $course->id]) }}";
							column_box.attr('action',newAction);
							@endif
						}
						if (i == (column - 1)) {
							
							$("#btnSubmitFormQuestion").prop('disabled',false);
							$("#preloaderFormQuestion").hide();
							if (data.status == 'success') {

							}
						}
					},
					error 	: function(error) {
						console.error(error.statusText);
					}
				});
			}
		})

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
			let column 	= active.attr('data-column');
			let row 	= active.attr('data-row');
			let element_id 	= `#questions_${column}_${row}`;
			editor 	= new FroalaEditor(element_id, {
				events: {
					'paste.after': function (original_event) {
						$.each($("#isPasted"),function(k,el) {
							if ($(el).prop("tagName").toLowerCase() != 'p') {
								$(el).closest('p')[0].outerHTML = '';
							}else{
								el.outerHTML = '';
							}
						})
					},
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
	})
</script>