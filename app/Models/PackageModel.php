<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use \App\Models\UserModel;

class PackageModel
{
    public $table = 'course_packages';
    public function get_all()
    {
        $packages    = DB::table($this->table)
        ->select("course_packages.*",DB::raw(" (SELECT token_key FROM token_test WHERE course_packages.id = token_test.course_package_id AND status = 1 ORDER BY date_created DESC LIMIT 1 )  as token  "))
        ->get();
        return $packages;
    }
    public function get($package_id)
    {
        $packages    = DB::table($this->table)
        ->where('id',$package_id)
        ->first();
        return $packages;
    }
    public function get_by($where)
    {
        $score    = DB::table($this->table)
        ->where($where)
        ->first();
        return $score;
    }
    public function get_course_to_category($package_id)
    {
        $packages    = DB::table("course_to_packages")
        ->select('course_to_packages.*','courses.name as course_name')
        ->join('courses','courses.id','=','course_to_packages.course_id')
        ->where('course_package_id',$package_id)
        ->get();
        return $packages;
    }
    public function get_token($package_id,$course_id)
    {
        $token_test    = DB::table("token_test")
        ->where('course_package_id',$package_id)
        ->where('course_id',$course_id)
        ->where('status',1)
        ->orderBy('date_created','desc')
        ->value('token_key');
        return !empty($token_test) ? $token_test : '';
    }
    public function insert($data)
    {
        $package_id                = $data["package_id"];
        $course_package_name       = $data["course_package_name"];
        $course_type_id            = $data["course_type_id"];
        $course_id_arr             = $data["course_id"];

        $data   = [
            "name"                  => $course_package_name,
        ];
        if (empty($package_id)) {
            $package_id     = DB::table($this->table)->insertGetId($data);
            $success        = $package_id;
        }else{
            $success         = DB::table($this->table)->where('id',$package_id)->update($data);
        }

        $success         = DB::table("course_to_packages")
        ->where('course_package_id',$package_id)
        ->whereNotIn('course_id',$course_id_arr)
        ->delete();

        foreach ($course_id_arr as $course_id) {
            $data_course_to_packages      = [
                "course_package_id" => $package_id,
                "course_id" => $course_id,
                "course_type_id" => $course_type_id,
            ];
            $check  = DB::table("course_to_packages")->where($data_course_to_packages)->first();
            if (empty($check)) {
                $success    = DB::table('course_to_packages')->insert($data_course_to_packages);
            }
        }

        return true;
    }
    public function update($where,$data)
    {
        $update    = DB::table($this->table)
        ->where($where)
        ->update($data);
        return $update;
    }
    public function delete($id)
    {
        $delete    = DB::table($this->table)->where('id',$id)->delete();
        return $delete;
    }
    public function generate_token($data)
    {
        $status         = 1;
        $characters         = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength   = strlen($characters);
        $randomString       = '';
        $length             = 6;
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $token_key      = $randomString;

        $data["status"]         = 1;
        $data["token_key"]     = $token_key;

        $course_package_id  = $data["course_package_id"];
        $course_id  = $data["course_id"];
        $update     = DB::table("token_test")->where('course_package_id',$course_package_id)->where('course_id',$course_id)->update(["status" => 0]);
        $insert     = DB::table('token_test')->insert($data);
        return $insert;
    }
}
