<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use \App\Models\AuthModel;

class AuthController extends Controller
{
	public function login(Request $request)
	{
		$data["title"]  = "Login";
		$data["nav_active"] 	= "login";
		return view('auth.login',$data);
	}
	public function login_process(Request $request)
	{
		$status             = "";
		$response           = "";
		$validation_error   = false;
		if(!empty($request->all()) && $request->method() == "POST"){
			$validator_field    = [
				'username'		=> ['required'],
				'password'		=> ['required'],
			];
			$validator_message  = [
				'username.required'		=> "Username dibutuhkan",
				'password.required'		=> "Password dibutuhkan",
			];
			$validator      = Validator::make($request->all(),$validator_field,$validator_message);
			if ($validator->fails()) {
				$response           = $validator->errors()->first();
				$status             = "error";
				$validation_error   = true;
			}


			if(!$validation_error){
				$username       = $request->input('username');
				$password   	= $request->input('password');
				$remember_me   	= $request->input('remember_me');

				$auth_model 	= new AuthModel();
				if ($auth_model->login($username,$password,$remember_me)) {

					if (isset($_SERVER['HTTP_COOKIE'])) {
						$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
						foreach($cookies as $cookie) {
							$parts = explode('=', $cookie);
							$name = trim($parts[0]);
							setcookie($name, '', time()-1000);
							setcookie($name, '', time()-1000, '/');
						}
					}
					
					$status     = "success";
					$response   = "Berhasil Login";
				}else{
					$status 	= "error";
					$response 	= "Username/Password Salah";
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

	public function logout(Request $request)
	{
		\Session::flush();
		if (isset($_SERVER['HTTP_COOKIE'])) {
			$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
			foreach($cookies as $cookie) {
				$parts = explode('=', $cookie);
				$name = trim($parts[0]);
				setcookie($name, '', time()-1000);
				setcookie($name, '', time()-1000, '/');
			}
		}
		return redirect(route('login'));
	}
}
