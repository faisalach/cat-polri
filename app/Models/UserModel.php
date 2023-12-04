<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class UserModel
{
    public $table = 'users';
    public function get($user_id)
    {
        $course    = DB::table($this->table)
        ->select("users.*",DB::raw("classes.name as class_name"),DB::raw("CONCAT(first_name,' ',last_name) as name"))
        ->leftJoin("classes","classes.id","=","users.class_id")
        ->where('users.id',$user_id)
        ->first();
        return $course;
    }

    public function insert($data)
    {
        $data["password_ori"]      = $data["password"];
        $data["password"]   = password_hash($data["password"], PASSWORD_DEFAULT);
        $data["email"]      = '';
        $data["profile_image"]      = '';
        $user_id    = DB::table($this->table)->insertGetId($data);
        return $user_id;
    }
    public function update($id,$data)
    {
        if (!empty($data["password"])) {
            $data["password_ori"]      = $data["password"];
            $data["password"]           = password_hash($data["password"], PASSWORD_DEFAULT);
        }
        $update    = DB::table($this->table)->where('id',$id)->update($data);
        return $update;
    }
    public function delete($id)
    {
        $delete    = DB::table($this->table)->where('id',$id)->delete();
        return $delete;
    }
}
