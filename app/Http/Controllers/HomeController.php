<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\ClassesModel;
use App\Models\UserModel;

class HomeController extends Controller
{
	public function dashboard(Request $request)
	{
		$count_siswa 		= 0;
		$count_pengajar 		= 0;
		$count_test 		= 0;

		$count_siswa 		= DB::table('users')->where('user_group_id',3)->count();
		$count_pengajar 	= DB::table('users')->where('user_group_id',2)->count();
		$count_ms 			= DB::table('users')->where('user_group_id',3)->where('final_score_psi','>=',61)->where('final_score_aka','>=',61)->count();
		$count_tms 			= DB::table('users')->where('user_group_id',3)->where('final_score_psi','<',61)->where('final_score_aka','<',61)->count();
		$count_test 		= DB::table('course_types')->count();

		$data["title"] 	= "Dashboard";
		$data["nav_active"]  	= "dashboard";
		$data["count_siswa"]  	= $count_siswa;
		$data["count_pengajar"]  	= $count_pengajar;
		$data["count_test"]  	= $count_test;
		$data["count_ms"]  	= $count_ms;
		$data["count_tms"]  	= $count_ms;
		return view('home.dashboard',$data);
	}
	public function users(Request $requests)
	{
		$data["title"] 	= "User";
		$data["nav_active"]  	= "users";
		return view('home.users',$data);	
	}
	public function users_get_datas(Request $request)
	{
		$search_arr 	= [DB::raw("CONCAT(first_name,' ',last_name)"),"username","password_ori","user_groups.name","classes.name"];

		$query 			= DB::table("users");
		$query->select(DB::raw("CONCAT(first_name,' ',last_name) as name"), "username","password_ori", "user_groups.name as user_group_name","classes.name as class_name","users.id","users.user_group_id");
		$query->join("user_groups","user_groups.id","=","users.user_group_id");
		$query->leftJoin("classes","classes.id","=","users.class_id");

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
			$responses["data"][] = $data;
		}


		return response()->json($responses);
	}
	public function users_create(Request $requests)
	{
		$classes_model 	= new ClassesModel();
		$classes 		= $classes_model->get_all();
		$data["title"] 	= "Buat User";
		$data["nav_active"]  	= "users";
		$data["classes"]  	= $classes;
		return view('home.users_create',$data);	
	}
	public function users_store (Request $request)
	{
		$status             = "";
		$response           = "";
		$validation_error   = false;
		if(!empty($request->all()) && $request->method() == "POST"){
			$validator_field    = [
				'first_name'		=> ['required','alpha_dash'],
				'username'			=> ['required','alpha_dash','unique:users'],
				'password'			=> ['required',"min:8"],
				'conf_password'		=> ['required',"required_with:password","same:password"],
			];
			$validator_message  = [
				'first_name.required'		=> "Nama Depan wajib diiisi",
				'username.required'			=> "Username wajib diiisi",
				'username.unique'			=> "Username sudah digunakan",
				'password.required'			=> "Password wajib diiisi",
				'conf_password.required'	=> "Konfirmasi Password wajib diiisi",
				'conf_password.same'		=> "Konfirmasi Password salah",
				'conf_password.required_with'	=> "Konfirmasi Password salah",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}


			if(!$validation_error){
				$first_name 	= $request->input('first_name');
				$last_name 		= $request->input('last_name');
				$class_id 		= $request->input('class_id');
				$username 		= $request->input('username');
				$password 		= $request->input('password');
				$user_group_id	= 3;
				$data 			= [
					"first_name" 		=> $first_name,
					"last_name" 		=> $last_name,
					"class_id" 			=> $class_id,
					"username" 			=> $username,
					"password" 			=> $password,
					"user_group_id" 	=> $user_group_id,
				];

				$user_model 	= new UserModel();
				if ($course_id = $user_model->insert($data)) {
					$status     = "success";
					$response   = "Berhasil membuat user";
				}else{
					$status 	= "error";
					$response 	= "Gagal membuat user";
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
	public function users_edit(Request $requests,$user_id)
	{
		$user_model 	= new UserModel();
		$user_obj 		= $user_model->get($user_id);
		if (empty($user_obj)) {
			return view('404');
		}

		$classes_model 	= new ClassesModel();
		$classes 		= $classes_model->get_all();

		$data["title"] 		= "Edit User";
		$data["nav_active"]  	= "users";
		$data["classes"]  	= $classes;
		$data["user_obj"]  	= $user_obj;
		return view('home.users_edit',$data);	
	}
	public function users_update (Request $request,$user_id)
	{
		$status             = "";
		$response           = "";
		$validation_error   = false;
		if(!empty($request->all()) && $request->method() == "POST"){
			$validator_field    = [
				'first_name'		=> ['required','alpha_dash'],
				// 'username'			=> ['required','alpha_dash','unique:users'],
				'password'			=> ["min:8"],
				'conf_password'		=> ["required_with:password","same:password"],
			];
			$validator_message  = [
				'first_name.required'		=> "Nama Depan wajib diiisi",
				// 'username.required'			=> "Username wajib diiisi",
				// 'username.unique'			=> "Username sudah digunakan",
				'conf_password.same'		=> "Konfirmasi Password salah",
				'conf_password.required_with'	=> "Konfirmasi Password salah",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}


			if(!$validation_error){
				$first_name 	= $request->input('first_name');
				$last_name 		= $request->input('last_name');
				$class_id 		= $request->input('class_id');
				$password 		= $request->input('password');
				$data 			= [
					"first_name" 		=> $first_name,
					"last_name" 		=> $last_name,
					"class_id" 			=> $class_id,
				];
				if (!empty($password)) {
					$data["password"] = $password;
				}

				$user_model 	= new UserModel();
				if ($course_id = $user_model->update($user_id,$data)) {
					$status     = "success";
					$response   = "Berhasil mengedit user";
				}else{
					$status 	= "error";
					$response 	= "Gagal mengedit user";
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
	public function users_delete(Request $request)
	{
		$status             = "";
		$response           = "";
		$validation_error   = false;
		if(!empty($request->all()) && $request->method() == "POST"){
			$validator_field    = [
				'remove_id'		=> ['required'],
			];
			$validator_message  = [
				'remove_id.required'		=> "User wajib dipilih",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}

			if(!$validation_error){
				$user_id 		= $request->input('remove_id');
				$user_model 	= new UserModel();
				if ($delete = $user_model->delete($user_id)) {
					$status     = "success";
					$response   = "Berhasil menghapus user";
				}else{
					$status 	= "error";
					$response 	= "Gagal menghapus user";
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
