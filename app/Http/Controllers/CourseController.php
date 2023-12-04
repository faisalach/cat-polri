<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use \App\Models\CourseModel;

class CourseController extends Controller
{
	public function index(Request $request)
	{
		$course_model		= new CourseModel();
        $courses            = $course_model->get_all();
        $courses_arr 		= [];
        foreach ($courses as $course) {
        	$courses_arr[$course->course_type_id]["name"] 	= $course->course_type_name;
        	$courses_arr[$course->course_type_id]["data"][$course->course_category_id]["isRandom"] 		= $course->isRandom;
        	$courses_arr[$course->course_type_id]["data"][$course->course_category_id]["course_category_id"] 		= $course->course_category_id;
        	$courses_arr[$course->course_type_id]["data"][$course->course_category_id]["name"] 		= $course->course_category_name;
        	$courses_arr[$course->course_type_id]["data"][$course->course_category_id]["data"][] 	= $course;
        }

		$data["courses"] 		= $courses_arr;
		$data["nav_active"]  	= "courses";
		$data["title"]  		= "Soal Tes";
		return view('courses.index',$data);
	}
	public function create()
	{
		$course_model 			= new CourseModel();
		$course_categories 		= $course_model->get_course_category();
		$course_categories_arr 	= [];
		foreach($course_categories as $category){
			$course_categories_arr[$category->course_type_id]["name"] = $category->course_type_name;
			$course_categories_arr[$category->course_type_id]["data"][] = $category;
		}

		$data["title"]  		= "Buat Soal";
		$data["nav_active"]  	= "courses";
		$data["course_categories"] 	= $course_categories_arr;
		return view('courses.create',$data);
	}
	public function store(Request $request)
	{
		$status             = "";
		$response           = "";
		$validation_error   = false;
		if(!empty($request->all()) && $request->method() == "POST"){
			$validator_field    = [
				'course_type_id'				=> ['required','numeric'],
				'course_category_id'			=> ['required','numeric'],
			];
			$validator_message  = [
				'course_type_id.required'		=> "jenis tes dibutuhkan",
				'course_type_id.numeric'		=> "Harap input angka pada form jenis tes",
				'course_category_id.required'	=> "nama tes dibutuhkan",
				'course_category_id.numeric'	=> "Harap input angka pada form nama tes",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}


			if(!$validation_error){
				$course_type_id   		= $request->input('course_type_id');
				$course_category_id   	= $request->input('course_category_id');
				$data_insert 			= [
					"course_type_id" 		=> $course_type_id,
					"course_category_id" 	=> $course_category_id,
				];
				$course_model 	= new CourseModel();
				if ($course_id = $course_model->insert($data_insert)) {
					$status     = "success";
					$response   = "Berhasil membuat soal";
				}else{
					$status 	= "error";
					$response 	= "Gagal membuat soal";
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
	public function update_israndom(Request $request)
	{
		$status             = "";
		$response           = "";
		$validation_error   = false;
		if(!empty($request->all()) && $request->method() == "POST"){
			$validator_field    = [
				'course_category_id'			=> ['required','numeric'],
			];
			$validator_message  = [
				'course_category_id.required'	=> "nama tes dibutuhkan",
				'course_category_id.numeric'	=> "Harap input angka pada form nama tes",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}


			if(!$validation_error){
				$course_category_id   	= $request->input('course_category_id');
				$isRandom   			= $request->input('isRandom');
				$course_model 	= new CourseModel();
				if ($course_id = $course_model->update_isRandom($course_category_id,$isRandom)) {
					$status     = "success";
					$response   = "Berhasil mengedit acak nomor";
				}else{
					$status 	= "error";
					$response 	= "Gagal mengedit acak nomor";
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

	public function delete(Request $request)
	{
		$status             = "";
		$response           = "";
		$validation_error   = false;
		if(!empty($request->all()) && $request->method() == "POST"){
			$validator_field    = [
				'remove_id'		=> ['required'],
			];
			$validator_message  = [
				'remove_id.required'		=> "Soal wajib dipilih",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}

			if(!$validation_error){
				$course_id 		= $request->input('remove_id');
				$course_model 	= new CourseModel();
				if ($delete = $course_model->delete($course_id)) {
					$status     = "success";
					$response   = "Berhasil menghapus soal";
				}else{
					$status 	= "error";
					$response 	= "Gagal menghapus soal";
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
}
