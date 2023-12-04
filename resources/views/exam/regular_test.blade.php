@include('partials.header_user')

<style>
	#number_box{
		display: flex;
		flex-wrap: wrap;
		min-height : 300px;
		height : calc(100vh - 325px);
		overflow-y: auto;
		margin-left: -10px;
		margin-right: -10px;
		justify-content: center;
		align-content: start;
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
		background-color: #888 !important;
		color: white !important;
	}
	#questions_box .card{
		display: none;
	}

	#questions_box .card.active{
		display: block;
		opacity: 1;
	}
	#backdrop{
		opacity: .5;
		position: fixed;
		top: 0;
		left: 0;
		z-index: 1040;
		right: 0;
		bottom: 0;
		background-color: #000;
	}
	#number_box .number.check{
		background-color: #ed7014;
		color: white;
	}
	.custom-control-label::before,.custom-control-label::after{
		left: -0.75rem;
	}
	.custom-control-input:checked~.custom-control-label::before{
		background-color: #ed7014;
		border-color: #ed7014;
	}
	.custom-checkbox .custom-control-input:indeterminate~.custom-control-label::before{
		background-color: transparent;
		border-color: #adb5bd;
	}
	.custom-checkbox .custom-control-input:indeterminate~.custom-control-label::after{
		background-image: none;
	}

	#formQuestion .custom-left{
		width: 269px;
		margin-left: 15px;
	}
	#formQuestion .custom-right{
		margin-right: 15px;
		flex: 1 1 auto ;
		width: calc(100% - 300px);
	}
</style>

<form action="" class="mt-3 px-3" method="post" id="formQuestion" enctype="multipart/form-data">
	@csrf
	<div class="row">
		<div class="{{ $course->course_category_id == 2 ? 'col-md-12' : 'custom-left' }}">
			<div class="" id="number_container">
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<strong><p class="mb-2">{{ $title }}</p></strong>
							</div>
						</div>
						<div class="row mb-2">
							<div class="col-md-12">
								<h5 id="timer" class="mb-3">00:00:00</h5>
							</div>
							<div class="col-md-12">
								<button class="btn btn-success btnSave" data-toggle="modal" data-target="#confirmationModal" type="button">Selesai <i class="fas fa-fw fa-save"></i></button>
							</div>
							@if($course->course_category_id != 2)
							<div class="col-md-12">
								<strong class="mr-3">Nomor Soal</strong>
							</div>
							@endif
						</div>
						@if($course->course_category_id != 2)
						<div id="number_box" style=""></div>
						@endif
					</div>
				</div>
			</div>	
		</div>
		<div class="{{ $course->course_category_id == 2 ? 'col-md-12' : 'custom-right' }}">
			<div class="" id="questions_box" style="display:none;"></div>
		</div>
	</div>
	<div class="modal fade" id="confirmationModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="staticBackdropLabel">Konfirmasi</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Apakah anda yakin menyelesaikan test ini?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
					<button class="btn btn-success" type="submit" id="btnSubmitFormQuestion">Selesai <i class="fas fa-fw fa-save"></i></button>
					<span class="spinner-border spinner-border-sm text-secondary" id="preloaderFormQuestion" style="display: none;" role="status">
						<span class="sr-only">Loading...</span>
					</span>
				</div>
			</div>
		</div>
	</div>
</form>
@include('partials.footer_user')

<script src="{{ url('assets') }}/js/jquery.form.min.js"></script>
<div id="backdrop" style="display: none;"></div>
<script>
	$(function(){
		let data 	= [];

		$("#number_container").show();
		$("#questions_box").show();
		$("#number_box").empty();
		$("#questions_box").empty();

		data 	= {!! json_encode($questions) !!};
		htmlNumber({{ $course->number_of_questions }});
		htmlQuestion({{ $course->number_of_questions }},data);
		$("[name=number_of_questions]").val({{ $course->number_of_questions }});
		@if($course->course_category_id != 2)
		$("body").on("click","#number_box .number",function(e) {
			let index 	= $("#number_box .number").index($(this));
			let index_active 	= $("#number_box .number").index($("#number_box .number.active"));
			if (index == index_active) {
				return false;
			}
			nextQuestion(index);
		});
		@endif

		$("body").on("change",".input-answer",function(e) {
			if ($(this).attr('type') == 'checkbox') {
				var card 		= $(this).closest('.card');
				var isChecked 	= $(this).is(":checked");
				if (isChecked) {
					if (card.find(".input-answer:checked").length >= 2) {
						if (card.find(".input-answer:checked").length > 2) {
							$(this).prop("checked",false);
						}
						setTimeout(function() {
							nextQuestion();
						},200)
					}
				}
			}else{
				setTimeout(function() {
					nextQuestion();
				},200)
			}
		});

		$("body").on("click","#questions_box .btnNext,#questions_box .btnBack",function(e) {
			let index 	= $("#questions_box .card").index($(this).parents('.card'));
			let crement 	= $(this).data('crement');
			let next_index 	= index + crement;

			$($("#number_box .number")[next_index]).click();
		});

		function checkmarkNumber() {
			let cards 	= $(`#questions_box .card`);
			$.each(cards,function(e,card) {
				let checkmark = $(card).find('.input-answer:checked');
				if (checkmark.length > 0) {
					// console.log($($("#number_box .number")[e]));
					$($("#number_box .number")[e]).addClass('check');
				}
			})
			saveAnswerToCookie();
		}

		function saveAnswerToCookie() {
			let cards 	= $(`#questions_box .card`);
			let data 	= [];
			$.each(cards,function(e,card) {
				let checkmark = $(card).find('.input-answer:checked');
				if (checkmark.length > 0) {
					let value 	= $(checkmark[0]).val();
					let value2 	= $(checkmark[1]).val();
					let id 		= checkmark.data('id');
					data.push({
						id : id,
						value : value,
						value2 : value2,
					})
				}
			})
			setCookie('answer_{{ $course->id }}',JSON.stringify(data),1);
		}
		function setAnswerFromCookie(){
			let answer 	= JSON.parse(getCookie('answer_{{ $course->id }}'));
			$.each(answer,function(k,data) {
				$(`#customCheck_${data.value}_${data.id}`).prop("checked",true);
				$(`#customCheck_${data.value2}_${data.id}`).prop("checked",true);
			})
			checkmarkNumber();
		}

		function countTimer(distance) {

			let hours = Math.floor(distance / (1000 * 60 * 60));
			let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			let seconds = Math.floor((distance % (1000 * 60)) / 1000);

			if (hours <= 9) {
				hours = "0"+hours;
			}
			if (minutes <= 9) {
				minutes = "0"+minutes;
			}
			if (seconds <= 9) {
				seconds = "0"+seconds;
			}
			let html 	= `Sisa Waktu : ${hours}:${minutes}:${seconds}`;
			$("#timer").html(`Sisa Waktu : ${hours}:${minutes}:${seconds}`);
			setCookie('timer_test_{{ $course->id }}', distance,1);
			return distance;
		}
		let x;
		function timer() {
			let countDownDate 	= new Date("{{ date('Y-m-d H:i:s', time() + ($course->test_time * 60)) }}").getTime();
			let now 	= new Date().getTime();
			let distance = countDownDate - now;

			let timer_test 	= getCookie('timer_test_{{ $course->id }}');
			if (timer_test != null && timer_test != '') {
				distance 	= timer_test;
			}
			countTimer(distance);
			x = setInterval(function() {
				distance 	-= 1000;
				distance = countTimer(distance);
				if (distance <= 0) {
					finishQuestion();
				}
			}, 1000);
		}
		timer();

		function finishQuestion() {
			clearInterval(x);
			eraseCookie('timer_test_{{ $course->id }}');
			$("#backdrop").show();
			$("#formQuestion").submit();
		}

		function setCookie(name,value,days) {
			var expires = "";
			if (days) {
				var date = new Date();
				date.setTime(date.getTime() + (days*24*60*60*1000));
				expires = "; expires=" + date.toUTCString();
			}
			document.cookie = name + "=" + (value || "")  + expires + "; path=/";
		}
		function getCookie(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i=0;i < ca.length;i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') c = c.substring(1,c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
			}
			return null;
		}
		function eraseCookie(name) {   
			document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
		}

		function nextQuestion(index = null) {
			if (index == null) {
				index = $('#questions_box .card').index($('#questions_box .card.active'));
				if (index == $('#questions_box .card').length - 1) {
					return false;
				}

				index 	= index + 1;
			}
			
			$("#number_box .number").removeClass("active");
			$("#questions_box .card").removeClass("active");

			$($("#number_box .number")[index]).addClass("active");
			setTimeout(function() {
				$($("#questions_box .card")[index]).addClass("active");
				checkmarkNumber();
			},300);
		}

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
				let html_button_left	= ``;
				let html_button_right	= ``;
				@if($course->course_category_id != 2)
				if(i > 0){
					html_button_left	= `<button class="btn btn-light btnBack" data-crement="-1" type="button"><i class="fas fa-fw fa-arrow-left"></i> Kembali</button>`;
				}
				if(i == count - 1){
					html_button_right 	= `<button class="btn btn-success btnSave" data-toggle="modal" data-target="#confirmationModal" type="button">Selesai <i class="fas fa-fw fa-save"></i></button>`;
				}else{
					html_button_right 	= `<button class="btn btn-secondary btnNext" data-crement="1" type="button">Selanjutnya <i class="fas fa-fw fa-arrow-right"></i></button>`;
				}
				@endif

				if(data[i] == undefined){
					html 	= `
					<div class="card ${ i == 0 ? 'active' : '' }" id="card_${i}">
						<div class="card-body">
							<div class="form-group">
								Soal tidak ada
							</div>
							@if($course->course_category_id != 2)
							<div class="row border-top pt-3 align-items-center">
								<div class="col-md-6">
									${html_button_left}
								</div>
								<div class="col-md-6 text-right">
									${html_button_right}
								</div>
							</div>
							@endif
						</div>
					</div>
					`;
					$("#questions_box").append(html);
				}else{

					let question_box 	= '';
					question_box 	= `
					<p>
					${data[i] != undefined ? data[i].question : '' }
					</p>
					`;

					// console.log(data[i].choice_type);
					

					html 	= `
					<div class="card ${ i == 0 ? 'active' : '' }" id="card_${i}">
						<div class="card-body">
							<strong class="mr-3">${ i + 1 }.</strong>
							<div style="max-height : calc(100vh - 290px);overflow-y:auto;overflow-x:hidden;min-height : 300px">
								<div class="form-group">
									${question_box}
								</div>
								<div class="form-group d-none">
									<p class="">Jawaban</p>
									@foreach(choice_option($course->id) as $key => $val)
									<label class="d-flex justify-content-center align-items-center form-group">
										<strong class="mr-3">{{ $val }}.</strong>
										<div class="w-100">
										${data[i] != undefined && data[i].choices['{{ $val }}'] != undefined ? data[i].choices['{{ $val }}'] : ''  }
										</div>
									</label>
									@endforeach
								</div>
							</div>
							<div class="form-group">
								<input name="answer[${data[i].id}]" value="0" type="hidden" class="mr-3">
								<table class="table text-center">
									<tr>
										@foreach(choice_option($course->id) as $val)
										<td class="p-0 border-0">
											<div class="d-block m-2">
												<div class="custom-control form-control-lg custom-radio">  
													<input data-id="${data[i].id}" name="${ data[i].choice_type == 'hybrid' ? `answer[${data[i].id}][]`: `answer[${data[i].id}]` }" value="{{ $val }}" type="${data[i].choice_type == 'hybrid' ? 'checkbox' : 'radio'  }" class="custom-control-input input-answer d-none inputAnswer_${data[i].id}" id="customCheck_{{$val}}_${data[i].id}">  
													<label class="custom-control-label" for="customCheck_{{$val}}_${data[i].id}"></label>
												</div>  
												<p class="mb-0">{{ $val }}</p>
											</div>
										</td>
										@endforeach
									</tr>
								</table>
							</div>
							@if($course->course_category_id != 2)
							<div class="row border-top pt-3 align-items-center">
								<div class="col-md-6">
									${html_button_left}
								</div>
								<div class="col-md-6 text-right">
									${html_button_right}
								</div>
							</div>
							@endif
						</div>
					</div>
					`;
					$("#questions_box").append(html);
					if ($("[data-f-id=pbf]").length > 0) {
						$.each($("[data-f-id=pbf]"),function(k,el) {
							el.outerHTML = '';
						})
					}


					let files 	= [];
					let initialPreviewConfig = [];
					if (data[i] != undefined && data[i].files != undefined && data[i].files.length > 0) {
						files 	= data[i].files;
						for(let j = 0; j < files.length;j++){
							let filename 	= files[j].replace('{{ url('uploads') }}/','');
							let html_append = `<input type='hidden' name='files_ready[${i}][]' value='${filename}'>`;
							initialPreviewConfig.push({
								caption : html_append
							});
						}
					}
				}


			}
			setAnswerFromCookie();
		}

		$("#formQuestion").ajaxForm({
			beforeSubmit : function() {
				$("#btnSubmitFormQuestion").prop('disabled',true);
				$("#preloaderFormQuestion").show();
				$("#confirmationModal [data-dismiss=modal]").hide();
				$("#backdrop").show();
			},
			dataType : "json",
			success : function(data) {
				$("#btnSubmitFormQuestion").prop('disabled',false);
				$("#preloaderFormQuestion").hide();
				if (data.status == 'success') {
					location.reload();
					eraseCookie('timer_test_{{$course->id}}');
				}else{
					notification(data.message,data.status);
				}
			},
			error 	: function(error) {
				console.error(error.statusText);
			}
		})
	})
</script>
