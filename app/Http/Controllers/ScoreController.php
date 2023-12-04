<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use \App\Models\CourseModel;
use \App\Models\AnswerModel;
use \App\Models\UserModel;
use \App\Models\ScoreModel;
use \App\Models\PackageModel;

class ScoreController extends Controller
{
    public function show(Request $request,$course_type_id,$course_package_id,$user_id = '')
    {
        $user_model             = new UserModel();
        $answer_model           = new AnswerModel();
        $package_model          = new PackageModel();
        $user_id                = !empty($user_id) ? $user_id : session('user_id');
        if (session('user_group_id') == 3) {
            $user_id            = session('user_id');
        }
        $user_obj               = $user_model->get($user_id);
        if (empty($user_obj) || $user_obj->user_group_id != 3) {
            return view('404');
        }
        $course_packages        = $package_model->get($course_package_id);
        $scores                 = $answer_model->getScores($user_id,$course_type_id,$course_package_id);

        $data["title"]          = "Score";
        $data["nav_active"]     = "score";
        $data["scores"]         = $scores;
        $data["user_obj"]       = $user_obj;
        $data["course_type_id"]       = $course_type_id;
        $data["course_packages"]      = $course_packages;
        return view('score.show',$data);
    }
    public function scores(Request $requests,$user_id)
    {
        $user_model             = new UserModel();
        $user_obj               = $user_model->get($user_id);
        $course_model           = new CourseModel();
        $courses                = $course_model->get_courses_siswa($user_id);

        $data["title"]          = "Score";
        $data["nav_active"]     = "scores";
        $data["courses"]        = $courses;
        $data["user_obj"]       = $user_obj;        
        return view('score.scores',$data);    
    }
    public function score_delete(Request $request)
    {
        $status             = "";
        $response           = "";
        $validation_error   = false;
        if(!empty($request->all()) && $request->method() == "POST"){
            $validator_field    = [
                'remove_id'     => ['required'],
            ];
            $validator_message  = [
                'remove_id.required'        => "User wajib dipilih",
            ];
            $validator      = Validator::make($request->all(),$validator_field,$validator_message);
            if ($validator->fails()) {
                $response           = $validator->errors()->first();
                $status             = "error";
                $validation_error   = true;
            }

            if(!$validation_error){
                $score_id       = $request->input('remove_id');
                $score_model     = new ScoreModel();
                if ($delete = $score_model->delete($score_id)) {
                    $status     = "success";
                    $response   = "Berhasil menghapus score";
                }else{
                    $status     = "error";
                    $response   = "Gagal menghapus score";
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
    public function detail_score(Request $request,$score_id)
    {
        $score_model   = new ScoreModel();
        $score         = $score_model->get($score_id);

        $course_model   = new CourseModel();
        $course          = $course_model->get($score->course_id);

        $answer_model   = new AnswerModel();
        $score          = $answer_model->getScore($score_id,$course->course_type_id);
        $course_category_name   = $course->course_category_name;

        $data["title"]      = $course_category_name;
        $data['course']     = $course;
        $data['score']      = $score;
        return view('score.detail_score',$data);
    }
    public function list(Request $requests)
    {
        $score_model            = new ScoreModel();
        $scores                 = $score_model->get_scores();

        $data["title"]          = "Hasil Tes";
        $data["nav_active"]     = "scores";
        $data["scores"]         = $scores;        
        return view('score.list',$data);    
    }
}
