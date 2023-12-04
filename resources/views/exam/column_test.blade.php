@include('partials.header_user')
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
</style>
<div class="container-fluid" id="number_container">
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<strong>{{ $title }}</strong>
				</div>
				<div class="col-md-6 text-md-right">
					<button class="btn btn-success btnSave" type="button">Selesai <i class="fas fa-fw fa-save"></i></button>
				</div>
			</div>
		</div>
	</div>
	<div class="card">
		<div class="card-body">
			<div class="row align-item-center">
				<div class="col-md-6">
					<strong class="mr-3" id="title_question"></strong>
				</div>
				<div class="col-md-6 text-md-right">
					<h3 id="timer">00:00:00</h3>
				</div>
			</div>
		</div>
	</div>
</div>
<form action="" id="formQuestion" method="post">
@csrf
<div class="container-fluid" id="questions_box">
	@for($i = 0; $i < count_course(3)[0]; $i++)
	<div style="display:none;" class="column_box" id="column_box_{{$i}}" >
		@for($j = 0; $j < count_course(3)[1]; $j++)
		@php
		$question_obj 	= !empty($questions[$i][$j]) ? $questions[$i][$j] : [];
		@endphp
		<div class="card {{ $i == 0 && $j == 0 ? 'active' : '' }}" id="card_{{$i}}_{{$j}}">
			<div class="card-body text-center">
				<!-- <label class="mr-3"><strong>{{$j+1}}.</strong></label> -->
				<div class="form-group d-flex justify-content-center">
					{!! !empty($question_obj->question) ? $question_obj->question : '' !!}
				</div>
				<div class="form-group mx-auto">
					<input name="answer[{{ !empty($question_obj->id) ? $question_obj->id : '' }}]" value="0" type="hidden" class="mr-3">
					<table class="table text-center" style="max-width:1000px;margin: auto;">
						<tr>
							@foreach(choice_option($course->id) as $val)
							<td class="p-0 border-0">
								<div class="d-block m-2">
									<div class="custom-control form-control-lg custom-radio">  
										<input name="answer[{{ !empty($question_obj->id) ? $question_obj->id : '' }}]" value="{{ $val }}" type="radio" class="custom-control-input input-answer d-none" id="customCheck_{{$val}}_{{ $i }}_{{ $j }}">  
										<label class="custom-control-label" for="customCheck_{{$val}}_{{ $i }}_{{ $j }}"></label>
									</div>  
									<p class="mb-0">{{ $val }}</p>
								</div>
							</td>
							@endforeach
						</tr>
					</table>
				</div>
			</div>
		</div>
		@endfor
	</div>
	@endfor
</div>
</form>
@include('partials.footer_user')
<script src="{{ url('assets') }}/js/jquery.form.min.js"></script>

<div id="backdrop" style="display: none;"></div>
<script>
	$(function() {
		$.each($("[data-f-id=pbf]"),function(k,el) {
			el.outerHTML = '';
		})
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
			},300);
		});

		$("body").on("click","#number_box .nav-link",function(e) {
			let index 	= $("#number_box .nav-link").index($(this));
			$(`#column_${index}`).find(".number.active").click();
		});

		$("body").on("click",".input-answer",function(e) {
			setTimeout(function() {
				nextQuestion();
			},100)
		});

		function getColumnIndexActive() {
			let index 			= $(".column_box").index($('.column_box:visible'));
			return index;
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
			// let distance = parseInt('{{ ($course->test_time) * 60 * 1000 }}') + 5 * 1000;
			let distance = parseInt('{{ ($course->test_time) * 60 * 1000 }}');
			// distance = '{{ 3 * 1000 }}';

			let timer_test 	= getCookie('timer_test_{{ $course->id }}');
			if (timer_test != null && timer_test != '') {
				distance 	= timer_test;
			}
			countTimer(distance);
			x = setInterval(function() {
				distance 	-= 1000;
				distance = countTimer(distance);
				if (distance <= 5000) {
					$("#backdrop").show();
				}
				if (distance <= 0) {
					finishColumn();
				}
			}, 1000);
		}
		nextColumn(1);
		function finishColumn() {
			clearInterval(x);
			eraseCookie('timer_test_{{ $course->id }}');
			$("#backdrop").show();

			let index 			= getColumnIndexActive();
			if (index == $(".column_box").length - 1) {
				$("#formQuestion").submit();
			}else{
				nextColumn();
				$("#backdrop").hide();
			}
		}

		function nextColumn(first = 0) {
			let index 			= getColumnIndexActive();

			let next_index 		= index + 1;
			if (first == 1) {
				let column_active 	= getCookie('column_active');
				if(column_active != null && column_active != '' && column_active < $(".column_box").length){
					next_index 		= parseInt(column_active);
				}
			}
			let timer_test 	= getCookie('timer_test_{{ $course->id }}');
			setCookie('column_active',next_index);

			$(".column_box").hide();
			let column_box_next 	= $($(".column_box")[next_index]);
			column_box_next.show();
			column_box_next.find('.card').removeClass('active');
			column_box_next.find('.card:first').addClass('active');

			// $("#title_question").html("Kolom "+ (next_index + 1));
			timer();
		}
		function nextQuestion() {
			let column_box 	= $('.column_box:visible');
			let index = column_box.find('.card').index(column_box.find('.card:visible'));
			if (index == column_box.find('.card').length - 1) {
				finishColumn();
				return false;
			}
			let next_index 	= index + 1;
			let card_next 	= $(column_box.find(".card")[next_index]);
			column_box.find('.card').removeClass('active');
			card_next.addClass('active');
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

		$("body").on("click",'.btnSave',function(e) {
			e.preventDefault();
			$(this).addClass('clicked');
			$("#formQuestion").submit();
		})

		$("#formQuestion").ajaxForm({
			dataType : "json",
			success : function(data) {
				eraseCookie('timer_test_{{$course->id}}');
				eraseCookie('column_active');
				if (data.status == 'success' && ((getColumnIndexActive() == $(".column_box").length - 1) || $(".btnSave").hasClass('clicked'))) {
					notification(data.message,data.status);
					setTimeout(function() {
						location.reload();
					},1000);
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