<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use \App\Models\CourseModel;
use \App\Models\QuestionModel;

class QuestionController extends Controller
{
	public function create(Request $request, $course_id)
	{
		$course_model 	= new CourseModel();
		$course 		= $course_model->get($course_id);

		if (empty($course)) {
			return view('404');
		}

		$question_model 	= new QuestionModel();
		$questions 			= $question_model->get_question($course_id);
        if (!empty($questions) && $course->course_category_id == 3) {
			$questions 		= restructure_question_for_columns($questions);
		}

		$data["title"]  		= "Pertanyaan";
		$data["nav_active"]  	= "courses";
		$data['course'] 		= $course;
		$data['questions'] 		= $questions;
		if ($course->course_category_id == 3) {
			return view('questions.column_test',$data);
		}else{
			return view('questions.regular_test',$data);
		}
	}
	public function store(Request $request,$course_id)
	{
		$course_model 		= new CourseModel();
		$course 			= $course_model->get($course_id);

		$status             = "";
		$response           = "";
		$validation_error   = false;
		if(!empty($request->all()) && $request->method() == "POST"){
			$validator_field    = [
				'test_time'				=> ['required',"numeric"],
				'number_of_questions'	=> ['required',"numeric"],
				'choices'				=> ['required','array'],
				'choices.*'				=> ['required'],
				'correct_choices'		=> ['required','array'],
				// 'correct_choices.*'		=> ['required'],
			];
			$validator_message  = [
				'choices.*.required' => "Opsi wajib diisi",
				'correct_choices.*.required' => "Jawaban Benar wajib ditandai",
				'number_of_questions.required' => "Wajib mengisi jumlah soal",
				'test_time.required' => "Wajib mengisi waktu pengerjaan soal",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}

			$course_sub_category_ids_arr 	= $request->input('sub_category_ids');
			$number_of_questions 	= $request->input('number_of_questions');
			$test_time 				= $request->input('test_time');
			$questions_arr 			= $request->input('questions');
			$choice_type_arr 		= $request->input('choice_type');
			$choices_arr 			= $request->input('choices');
			$correct_choices 		= $request->input('correct_choices');
			$files_arr 				= $request->input('files');

			$files 					= [];
			if (!$validation_error && !empty($files_arr)) {
				foreach ($files_arr as $key => $file_arr) {
					if (empty($file_arr)) {
						continue;
					}
					$count_file 	= count($file_arr["file"]);
					for ($i=0; $i < $count_file; $i++) { 
						$file 	= $file_arr["file"][$i];
						$name 	= $file_arr["name"][$i];
						$filesize 	= getSizeFile($file);
						$check 	= validate_upload_file("image",$name,$filesize);
						if ($check["error"]) {
							$validation_error 	= true;
							$status 			= $check["status"];
							$message 			= "Soal Nomor $key ke-$key2  : " . $check["message"];
						}else{
							$files[$key][] 	= [
								"name" 		=> $name,
								"file" 		=> get_base64_file($file),
							];
						}
					}
				}
			}

			if(!$validation_error){
				$question_model 	= new QuestionModel();
				$data_insert 		= [];

				$sub_categories 	= $course_model->get_course_sub_category($course->course_category_id);
				$course_sub_category_id = !empty($sub_categories[0]->id) ? $sub_categories[0]->id : 0;

				for ($i=0; $i < $number_of_questions; $i++) {
					$course_sub_category_id = !empty($course_sub_category_ids_arr[$i]) ? $course_sub_category_ids_arr[$i] : $course_sub_category_id;

					$data_for 	= [
						"course_id" => $course_id,
						"number" => ($i + 1),
						"question" 			=> !empty($questions_arr[$i]) ? $questions_arr[$i] : "",
						"choice_type"		=> !empty($choice_type_arr[$i]) ? $choice_type_arr[$i] : "",
						"correct_choice" 	=> !empty($correct_choices[$i][0]) ? $correct_choices[$i][0] : "",
						"correct_choice_2" 	=> !empty($correct_choices[$i][1]) ? $correct_choices[$i][1] : "",
						"course_sub_category_id" 	=> $course_sub_category_id,
						"choices" 			=> !empty($choices_arr[$i]) ? $choices_arr[$i] : "",
						"files" 			=> !empty($files[$i]) ? $files[$i] : [],
					];
					$data_insert[] 	= $data_for;
				}


				if ($question_id = $question_model->insert($data_insert)) {
					$update = $course_model->update(["id" => $course_id],[
						"number_of_questions" => $number_of_questions,
						"test_time" => $test_time,
					]);
					$status     = "success";
					$response   = "Berhasil membuat pertanyaan";
				}else{
					$status 	= "error";
					$response 	= "Gagal membuat pertanyaan";
				}
			}
		}else{
			$status     = "error";
			$response   = "Invalid access";
		}

		$responses  = [
			"status"     => $status,
			"message"   => $response,
		];

		return response()->json($responses);
	}

	public function update(Request $request,$course_id)
	{
		$status             = "";
		$response           = "";
		$validation_error   = false;
		if(!empty($request->all()) && $request->method() == "POST"){
			$validator_field    = [
				'number_of_questions'	=> ['required',"numeric"],
				'test_time'				=> ['required','numeric'],
				'choices'				=> ['required','array'],
				'choices.*'				=> ['required'],
				'correct_choices'		=> ['required','array'],
				// 'correct_choices.*'		=> ['required'],
			];
			$validator_message  = [
				'choices.*.required' => "Opsi wajib diisi",
				'correct_choices.*.required' => "Jawaban Benar wajib ditandai",
				'number_of_questions.required' => "Wajib mengisi jumlah soal",
				'test_time.required' => "Wajib mengisi waktu pengerjaan soal",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}

			$course_sub_category_ids_arr 	= $request->input('sub_category_ids');
			$number_of_questions 	= $request->input('number_of_questions');
			$test_time 				= $request->input('test_time');
			$questions_arr 			= $request->input('questions');
			$choice_type_arr 		= $request->input('choice_type');
			$choices_arr 			= $request->input('choices');
			$correct_choices 		= $request->input('correct_choices');
			$files_ready			= $request->input('files_ready');
			$files_arr 				= $request->input('files');

			$files 					= [];
			if (!$validation_error && !empty($files_arr)) {
				foreach ($files_arr as $key => $file_arr) {
					if (empty($file_arr)) {
						continue;
					}
					$count_file 	= count($file_arr["file"]);
					for ($i=0; $i < $count_file; $i++) { 
						$file 	= $file_arr["file"][$i];
						$name 	= $file_arr["name"][$i];
						$filesize 	= getSizeFile($file);
						$check 	= validate_upload_file("image",$name,$filesize);
						if ($check["error"]) {
							$validation_error 	= true;
							$status 			= $check["status"];
							$message 			= "Soal Nomor $key ke-$key2  : " . $check["message"];
						}else{
							$files[$key][] 	= [
								"name" 		=> $name,
								"file" 		=> get_base64_file($file),
							];
						}
					}
				}
			}

			if(!$validation_error){
				$question_model 	= new QuestionModel();
				$course_model 		= new CourseModel();
				$data_update 		= [];
				$course 			= $course_model->get($course_id);

				$sub_categories 	= $course_model->get_course_sub_category($course->course_category_id);
				$course_sub_category_id = !empty($sub_categories[0]->id) ? $sub_categories[0]->id : 0;

				for ($i=0; $i < $number_of_questions; $i++) { 
					$course_sub_category_id = !empty($course_sub_category_ids_arr[$i]) ? $course_sub_category_ids_arr[$i] : $course_sub_category_id;
					$data_for 	= [
						"course_id" => $course_id,
						"number" => ($i + 1),
						"question" 			=> !empty($questions_arr[$i]) ? $questions_arr[$i] : "",
						"choice_type" 		=> !empty($choice_type_arr[$i]) ? $choice_type_arr[$i] : "",
						"correct_choice" 	=> !empty($correct_choices[$i][0]) ? $correct_choices[$i][0] : "",
						"correct_choice_2" 	=> !empty($correct_choices[$i][1]) ? $correct_choices[$i][1] : "",
						"course_sub_category_id" 	=> $course_sub_category_id,
						"choices" 			=> !empty($choices_arr[$i]) ? $choices_arr[$i] : "",
						"files" 			=> !empty($files[$i]) ? $files[$i] : [],
						"files_ready" 		=> !empty($files_ready[$i]) ? $files_ready[$i] : [],
					];
					$data_update[] 	= $data_for;
				}


				if ($question_id = $question_model->update($data_update)) {
					$update = $course_model->update(["id" => $course_id],[
						"number_of_questions" => $number_of_questions,
						"test_time" => $test_time,
					]);

					$status     = "success";
					$response   = "Berhasil menyimpan pertanyaan";
				}else{
					$status 	= "error";
					$response 	= "Gagal menyimpan pertanyaan";
				}
			}
		}else{
			$status     = "error";
			$response   = "Invalid access";
		}

		$responses  = [
			"status"     => $status,
			"message"   => $response,
		];

		return response()->json($responses);
	}

	public function store_column(Request $request,$course_id)
	{
		$status             = "";
		$response           = "";
		$validation_error   = false;
		if(!empty($request->all()) && $request->method() == "POST"){

			$validator_field    = [
				'choices'				=> ['required','array'],
				'choices.*'				=> ['required'],
				'correct_choices'		=> ['required','array'],
				// 'correct_choices.*'		=> ['required'],
				'questions'				=> ['required','array'],
				'questions.*'			=> ['required'],
			];
			$validator_message  = [
				'choices.required' => "Opsi wajib diisi",
				'choices.array' => "Opsi wajib diisi",
				'correct_choices.*.required' => "Jawaban Benar wajib ditandai",
				'correct_choices.*.array' => "Jawaban Benar wajib ditandai",
				'questions.*.required' => "Pertanyaan wajib diisi",
				'questions.*.array' => "Pertanyaan wajib diisi",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}


			if(!$validation_error){
				$questions_arr 			= $request->input('questions');
				$choices_arr 			= $request->input('choices');
				$correct_choices 		= $request->input('correct_choices');
				$data_insert 			= [];
				$count 					= count_course(3)[1];


				foreach($correct_choices as $key => $val){
					for ($i=0; $i < $count; $i++) { 
						if (empty($correct_choices[$key][$i])) {
							continue;
						}
						$number 		= $key * $count + ($i + 1);
						$question 		= !empty($questions_arr[$key][$i]) ? ($questions_arr[$key][$i]) : "";
						$data_for 	= [
							"course_id" 		=> $course_id,
							"number" 			=> $number,
							"question" 			=> $question,
							"correct_choice" 	=> !empty($correct_choices[$key][$i]) ? $correct_choices[$key][$i] : "",
							"choices" 			=> !empty($choices_arr[$key][$i]) ? $choices_arr[$key][$i] : "",
						];
						$data_insert[] 	= $data_for;
					}
				}

				$question_model 	= new QuestionModel();
				if ($course_id = $question_model->insert($data_insert)) {
					$status     = "success";
					$response   = "Berhasil membuat pertanyaan";
				}else{
					$status 	= "error";
					$response 	= "Gagal membuat pertanyaan";
				}
			}
		}else{
			$status     = "error";
			$response   = "Invalid access";
		}

		$responses  = [
			"status"     => $status,
			"message"   => $response,
			"course_id"   => !empty($course_id) ? $course_id : 0,
		];

		return response()->json($responses);
	}

	public function update_column(Request $request,$course_id)
	{
		$status             = "";
		$response           = "";
		$validation_error   = false;
		if(!empty($request->all()) && $request->method() == "POST"){
			$validator_field    = [
				'choices'				=> ['required','array'],
				'choices.*'				=> ['required'],
				'correct_choices'		=> ['required','array'],
				// 'correct_choices.*'		=> ['required'],
				'questions'				=> ['required','array'],
				'questions.*'			=> ['required'],
			];
			$validator_message  = [
				'choices.required' => "Opsi wajib diisi",
				'choices.array' => "Opsi wajib diisi",
				'correct_choices.*.required' => "Jawaban Benar wajib ditandai",
				'correct_choices.*.array' => "Jawaban Benar wajib ditandai",
				'questions.*.required' => "Pertanyaan wajib diisi",
				'questions.*.array' => "Pertanyaan wajib diisi",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}

			if(!$validation_error){

				$questions_arr 			= $request->input('questions');
				$choices_arr 			= $request->input('choices');
				$correct_choices 		= $request->input('correct_choices');
				$data_update 			= [];
				$count 					= count_course(3)[1];

				foreach($correct_choices as $key => $val){
					for ($i=0; $i < $count; $i++) {
						if (empty($correct_choices[$key][$i])) {
							continue;
						}
						$number 		= $key * $count + ($i + 1);
						$question 		= !empty($questions_arr[$key][$i]) ? ($questions_arr[$key][$i]) : "";
						$data_for 	= [
							"course_id" 		=> $course_id,
							"number" 			=> $number,
							"question" 			=> $question,
							"correct_choice" 	=> !empty($correct_choices[$key][$i]) ? $correct_choices[$key][$i] : "",
							"choices" 			=> !empty($choices_arr[$key][$i]) ? $choices_arr[$key][$i] : "",
						];
						$data_update[] 	= $data_for;
					}
				}


				$question_model 	= new QuestionModel();
				if ($question_model->update($data_update)) {
					$status     = "success";
					$response   = "Berhasil menyimpan pertanyaan";
				}else{
					$status 	= "error";
					$response 	= "Gagal menyimpan pertanyaan";
				}
			}
		}else{
			$status     = "error";
			$response   = "Invalid access";
		}

		$responses  = [
			"status"     => $status,
			"message"   => $response,
			"course_id"   => !empty($course_id) ? $course_id : 0,
		];

		return response()->json($responses);
	}
}
