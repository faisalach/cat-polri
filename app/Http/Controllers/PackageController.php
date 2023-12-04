<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use \App\Models\CourseModel;
use \App\Models\PackageModel;

class PackageController extends Controller
{
	public function index(Request $request)
	{
		$package_model			= new PackageModel();

		$data["nav_active"]  	= "packages";
		$data["title"]  		= "Paket Ujian";
		return view('packages.index',$data);
	}
	public function edit(Request $request, $package_id = '')
	{
		$package_model			= new PackageModel();
		$packages 				= $package_model->get($package_id);

		$course_to_packages 	= $package_model->get_course_to_category($package_id);
		$course_to_packages_arr = [];
		foreach($course_to_packages as $course_to_package){
			$course_to_packages_arr[$course_to_package->course_id] 	= true;
			$packages->course_type_id 	= $course_to_package->course_type_id;
		}

		$course_model		= new CourseModel();
		$courses            = $course_model->get_all();
		$courses_arr 		= [];
		foreach ($courses as $course) {
			$courses_arr[$course->course_type_id]["name"] 	= $course->course_type_name;
			$courses_arr[$course->course_type_id]["data"][$course->course_category_id]["name"] 		= $course->course_category_name;
			$courses_arr[$course->course_type_id]["data"][$course->course_category_id]["data"][] 	= $course;
		}

		$course_types            = $course_model->get_course_types();


		$data["title"]  			= "Edit Paket";
		$data["nav_active"]  		= "packages";
		$data["courses"] 			= $courses_arr;
		$data["package"] 			= $packages;
		$data["course_types"] 		= $course_types;
		$data["course_to_packages"]	= $course_to_packages_arr;
		return view('packages.edit',$data);
	}
	public function update(Request $request, $package_id = '')
	{
		$status             = "";
		$response           = "";
		$validation_error   = false;
		if(!empty($request->all()) && $request->method() == "POST"){

			$validator_field    = [
				'course_package_name'			=> ['required'],
				'course_type_id'				=> ['required'],
				'course_id'						=> ['required'],
			];
			$validator_message  = [
				'course_package_name.required'	=> "Nama paket dibutuhkan",
				'course_type_id.required'		=> "Harap pilih tipe soalnya",
				'course_id.required'			=> "Harap pilih soal soalnya",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}


			if(!$validation_error){
				$course_package_name   	= $request->input('course_package_name');
				$course_id   			= $request->input('course_id');
				$course_type_id   		= $request->input('course_type_id');
				$data_insert 			= [
					"package_id" 				=> $package_id,
					"course_package_name" 		=> $course_package_name,
					"course_type_id" 			=> $course_type_id,
					"course_id" 				=> $course_id,
				];
				$package_model 	= new PackageModel();
				if ($package_model->insert($data_insert)) {
					$status     = "success";
					$response   = "Berhasil mengedit paket";
				}else{
					$status 	= "error";
					$response 	= "Gagal mengedit paket";
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
	public function generate_token(Request $request, $course_package_id,$course_id)
	{
		$package_model			= new PackageModel();
		$data_insert 	= [
			"course_package_id" => $course_package_id,
			"course_id" => $course_id,
		];
		if ($package_model->generate_token($data_insert)) {
			$status     = "success";
			$response   = "Berhasil generate token";
		}else{
			$status 	= "error";
			$response 	= "Gagal generate token";
		}

		$responses  = [
			"status"     => $status,
			"message"   => $response,
		];

		return response()->json($responses);
	}
	public function package_get_datas(Request $request)
	{
		$package_model			= new PackageModel();
		$search_arr 	= ["course_packages.name","course_packages.name","course_packages.id"];

		$query 			= \DB::table("course_packages");
		$query->select("course_packages.name","course_packages.id");

		$recordsTotal 	= $query->count();

		$draw 			= $request->draw;
		$order 			= $request->order;
		$orderColumn 	= $order[0]['column'];
		$orderDir 		= $order[0]['dir'];
		$start 			= $request->start;
		$length 		= $request->length;
		$search 		= $request->search;
		$keyword 		= $search['value'];

		$query->where(function($query) use ($search_arr,$keyword)
		{
			foreach($search_arr as $key => $column){
				$query->orWhere($column, "LIKE", "%$keyword%");
			}
		});
		$recordsFiltered 	= $query->count();
		$query->orderBy($search_arr[$orderColumn],$orderDir);
		$query->offset($start);
		$query->limit($length);
		$results 	= $query->get();

		$responses = [
			"draw" => $draw,
			"recordsTotal" => $recordsTotal,
			"recordsFiltered" => $recordsFiltered,
			"data" => []
		];

		foreach ($results as $key=> $data) {
			$course_to_packages 	= $package_model->get_course_to_category($data->id);
			$data_child 			= [];
			foreach ($course_to_packages as $key => $course_to_package) {

				$course_id 		= $course_to_package->course_id;
				$course_name 	= $course_to_package->course_name;

				$token 			= $package_model->get_token($data->id,$course_id);

				$data_child[] 	= [
					"course_id" 	=> $course_id,
					"course_name" 	=> $course_name,
					"token" 		=> $token,
				];
			}
			$data->data_child 	= $data_child;

			$responses["data"][] = $data;
		}


		return response()->json($responses);
	}
	public function detail_package_get_datas(Request $request,$package_id)
	{
		$package_model			= new PackageModel();
		$search_arr 	= ["courses.name","courses.id","courses.id"];

		$query 			= \DB::table("course_to_packages");
		$query->select('course_to_packages.*','courses.name as course_name');
        $query->join('courses','courses.id','=','course_to_packages.course_id');
        $query->where('course_package_id',$package_id);

		$recordsTotal 	= $query->count();

		$draw 			= $request->draw;
		$order 			= $request->order;
		$orderColumn 	= $order[0]['column'];
		$orderDir 		= $order[0]['dir'];
		$start 			= $request->start;
		$length 		= $request->length;
		$search 		= $request->search;
		$keyword 		= $search['value'];

		$query->where(function($query) use ($search_arr,$keyword)
		{
			foreach($search_arr as $key => $column){
				$query->orWhere($column, "LIKE", "%$keyword%");
			}
		});
		$recordsFiltered 	= $query->count();
		$query->orderBy($search_arr[$orderColumn],$orderDir);
		$query->offset($start);
		$query->limit($length);
		$results 	= $query->get();

		$responses = [
			"draw" => $draw,
			"recordsTotal" => $recordsTotal,
			"recordsFiltered" => $recordsFiltered,
			"data" => []
		];

		foreach ($results as $key=> $data) {
			$token 			= $package_model->get_token($data->course_package_id,$data->course_id);
			$data->token 	= $token;

			$responses["data"][] = $data;
		}


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
				'remove_id.required'		=> "Paket wajib dipilih",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}

			if(!$validation_error){
				$package_id 		= $request->input('remove_id');
				$package_model 	= new PackageModel();
				if ($delete = $package_model->delete($package_id)) {
					$status     = "success";
					$response   = "Berhasil menghapus paket";
				}else{
					$status 	= "error";
					$response 	= "Gagal menghapus paket";
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
