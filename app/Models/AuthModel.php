<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class AuthModel
{
    public function login($username,$password,$remember_me)
    {
        $user_obj   = DB::table('users')->where('username',$username)->orWhere('email',$username)->first();
        if (!empty($user_obj)) {
            if (password_verify($password, $user_obj->password)) {
                $this->_set_session($user_obj);
                if (!empty($remember_me)) {
                    
                }
                return true;
            }
        }

        return false;
    }

    public function _set_session($user_obj)
    {
        $data["user_id"]    = $user_obj->id;
        $data["user_group_id"]    = $user_obj->user_group_id;
        $data["username"]   = $user_obj->username;
        $data["email"]      = $user_obj->email;
        $data["last_name"]  = $user_obj->last_name;
        $data["first_name"] = $user_obj->first_name;
        $name               = $user_obj->first_name;
        if (!empty($user_obj->last_name)) {
            $name           .= " ".$user_obj->last_name;
        }
        $data["name"]      = $name;

        session($data);
        return true;
    }

    public function _remember_me($user_id)
    {
        $generate_token     = $this->_random_string();
        $data       = [
            "key"   => $generate_token,
            "user_id" => $user_id
        ];
        $insert     = DB::table('remember_me')->insert($data);

        if ($insert) {
            $this->withCookie('cat_token', $generate_token);
            return true;
        }
        return false;
    }

    public function _random_string()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            $randstring = $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }
}
