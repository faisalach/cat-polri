<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use \App\Models\UserModel;

class CourseModel
{
    public $table = 'courses';
    public function get_all()
    {
        $courses    = DB::table($this->table)
        ->select("courses.*",DB::raw("course_category.name as course_category_name"),"course_category.isRandom",DB::raw("course_types.name as course_type_name"))
        ->join("course_category","course_category.id","=","courses.course_category_id")
        ->leftJoin("course_types","course_types.id","=","courses.course_type_id")
        ->get();
        return $courses;
    }
    public function get_courses_siswa($user_id = '',$package_id = '')
    {
        if (empty($user_id)) {
            $user_id    = session('user_id');
        }
        $user_model     = new UserModel();
        $user_obj       = $user_model->get($user_id);

        $query    = DB::table($this->table)
        ->select("courses.*",DB::raw("course_category.name as course_category_name"),DB::raw("course_types.name as course_type_name"),"course_package_id","course_packages.name as course_package_name")
        ->join("course_to_packages","course_to_packages.course_id","=","courses.id")
        ->join("course_packages","course_packages.id","=","course_to_packages.course_package_id")
        ->join("course_category","course_category.id","=","courses.course_category_id")
        ->join("course_types","course_types.id","=","courses.course_type_id");
        if ($user_obj->user_group_id == 3) {
            $query
            ->leftJoin("course_classes","course_classes.course_id","=","courses.id")
            ->where('class_id',null)
            ->orWhere('class_id',$user_obj->class_id);
        }

        $courses = $query->orderBy('course_category.id','asc')->get();

        $course_arr             = [];
        foreach($courses as $course_obj){
            $course_obj->score  = 0;
            if (!empty($package_id) && $course_obj->course_package_id == $package_id) {
                $course_arr[$course_obj->course_type_id]["name"] = $course_obj->course_type_name;
                $course_arr[$course_obj->course_type_id]["data"][] = $course_obj;
            }else{

                $course_arr[$course_obj->course_package_id]["name"] = $course_obj->course_package_name;
                $course_arr[$course_obj->course_package_id]["data"][$course_obj->course_type_id]["name"] = $course_obj->course_type_name;
                $course_arr[$course_obj->course_package_id]["data"][$course_obj->course_type_id]["data"][] = $course_obj;
            }
        }
        return $course_arr;
    }
    public function get($course_id)
    {
        $course    = DB::table($this->table)
        ->select("courses.*",DB::raw("course_category.name as course_category_name"))
        ->join("course_category","course_category.id","=","courses.course_category_id")
        ->where('courses.id',$course_id)
        ->first();
        return $course;
    }
    public function get_course_types()
    {
        $course    = DB::table("course_types")
        ->get();
        return $course;
    }
    public function get_course_category()
    {
        $course    = DB::table("course_category")
        ->select('course_category.*',DB::raw("course_types.name as course_type_name"))
        ->join('course_types','course_types.id', '=', 'course_category.course_type_id')
        ->get();
        return $course;
    }
    public function get_course_sub_categpry($course_category_id)
    {
        $course    = DB::table("course_sub_category")
        ->where('course_sub_category.course_category_id',$course_category_id)
        ->get();
        return $course;
    }
    public function insert($data)
    {
        $test_time          = time_course($data['course_category_id']);
        $number_of_questions    = count_course($data['course_category_id']);
        if (is_array($number_of_questions)) {
            $number_of_questions    = $number_of_questions[0] * $number_of_questions[1];
        }

        $course_category    = DB::table('course_category')
        ->select('name',DB::raw(" (SELECT name FROM courses where course_category.id = courses.course_category_id ORDER BY date_created DESC LIMIT 1) as last_name "))
        ->where('id',$data['course_category_id'])
        ->first();
        $name               = $course_category->name;
        $last_name          = $course_category->last_name;
        $total_course       = preg_replace("/[^0-9]/", "", $last_name);
        $total_course       = !empty($total_course) ? $total_course : 0;
        $total_course       = intval($total_course) + 1;
        $name               = $name . ' '. $total_course;

        $data   = [
            "name"                  => $name,
            "test_time"             => $test_time,
            "number_of_questions"   => $number_of_questions,
            "number_of_choice"      => $data['course_category_id'] == 2 ? 4 : 5,
            "course_category_id"    => $data['course_category_id'],
            "course_type_id"        => $data['course_type_id']
        ];
        $insert    = DB::table($this->table)
        ->insertGetId($data);
        return $insert;
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
    public function get_course_sub_category($course_category_id)
    {
        return DB::table("course_sub_category")->where('course_category_id',$course_category_id)->get();
    }
    public function update_isRandom($course_category_id,$isRandom)
    {
        return DB::table('course_category')->where('id',$course_category_id)->update(["isRandom" => $isRandom]);
    }
}
