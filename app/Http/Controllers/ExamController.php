<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use \App\Models\CourseModel;
use \App\Models\QuestionModel;
use \App\Models\AnswerModel;
use \App\Models\UserModel;

class ExamController extends Controller
{
    public function list(Request $request)
    {

        $user_model             = new UserModel();
        $user_obj               = $user_model->get(session('user_id'));
        $course_model           = new CourseModel();
        $courses                = $course_model->get_courses_siswa();
        // dd($courses);

        $data["title"]          = "Soal Test";
        $data["nav_active"]     = "exam";
        $data["courses"]        = $courses;
        $data["user_obj"]       = $user_obj;
        return view('exam.list',$data);
    }
    public function exam(Request $request,$course_id,$package_id,$token = '')
    {
        $user_id        = session('user_id');
        $course_model   = new CourseModel();
        $course         = $course_model->get($course_id);

        if (empty($course)) {
            return view('404');
        }


        $question_model     = new QuestionModel();
        $questions          = $question_model->get_question_for_exam($course_id);

        if (empty($questions)) {
            return view('404');
        }


        if (!empty($questions) && $course->course_category_id == 3) {
            $questions  = restructure_question_for_columns($questions);
        }

        $course_category_name   = $course->course_category_name;

        if ($score_id   = isAnswered($course_id,$user_id,$package_id)) {
            $answer_model       = new AnswerModel();
            $score              = $answer_model->getScore($score_id,$course->course_type_id);
            $data["title"]      = $course_category_name;
            $data["nav_active"] = "exam";
            $data['course']     = $course;
            $data['score']      = $score;
            return view('exam.score',$data);
        }

        $answer_model   = new AnswerModel();
        $check_token = $answer_model->check_token($token, $course_id,$package_id);
        if (empty($token) || !$check_token) {
            return redirect(route('exam.token',['course_id' => $course_id, "course_package_id" => $package_id]));
        }

        $data["title"]      = $course_category_name;
        $data["nav_active"] = "hide_menu";
        $data['course']     = $course;
        $data['questions']  = $questions;

        $user_model             = new UserModel();
        $user_obj               = $user_model->get($user_id);
        setcookie("userLog_1", base64_encode($user_obj->username) , time() + (24 * 60 * 60));
        setcookie("userLog_2", base64_encode($user_obj->password_ori) , time() + (24 * 60 * 60));

        if ($course->course_category_id == 3) {
            return view('exam.column_test',$data);
        }else{
            return view('exam.regular_test',$data);
        }
    }
    public function store(Request $request,$course_id,$package_id)
    {
        $status             = "";
        $response           = "";
        $validation_error   = false;
        if(!empty($request->all()) && $request->method() == "POST"){
            $validator_field    = [
                'answer'               => ['required','array'],
                'answer.*'             => ['required'],
            ];
            $validator_message  = [
                'answer.*.required' => "Anda harus mengisi jawaban",
            ];
            $validator      = Validator::make($request->all(),$validator_field,$validator_message);
            if ($validator->fails()) {
                $response           = $validator->errors()->first();
                $status             = "error";
                $validation_error   = true;
            }

            if(!$validation_error){
                $answer_arr     = $request->input('answer');
                $answer_model   = new AnswerModel();
                $scores      = $answer_model->count_score($answer_arr, $course_id);
                if ($insert = $answer_model->insert($answer_arr)) {
                    setcookie("userLog_1", '' , time() - (24 * 60 * 60));
                    setcookie("userLog_2", '' , time() - (24 * 60 * 60));
                    foreach($scores as $course_sub_category_id => $score){
                        $answer_model->save_score($course_id,$course_sub_category_id,$score,$package_id);
                    }

                    $status     = "success";
                    $response   = "Berhasil menjawab pertanyaan";

                    session(['exam_code' => '']);
                }else{
                    $status     = "error";
                    $response   = "Gagal menjawab pertanyaan";
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

        echo json_encode($responses);
        exit;
    }
    public function token(Request $request,$course_id,$package_id,$token = '')
    {
        $answer_model   = new AnswerModel();
        $check_token = $answer_model->check_token($token, $course_id,$package_id);
        if (!empty($token) && $check_token) {
            return redirect(route('exam.exam',['course_id' => $course_id, "course_package_id" => $package_id,"token" => $token]));
        }
        if(!empty($request->all()) && $request->method() == "POST"){
            $status             = "";
            $response           = "";
            $validation_error   = false;

            $validator_field    = [
                'token'             => ['required'],
            ];
            $validator_message  = [
                'token.required'    => "Token wajib diisi",
            ];
            $validator      = Validator::make($request->all(),$validator_field,$validator_message);
            if ($validator->fails()) {
                $response           = $validator->errors()->first();
                $status             = "error";
                $validation_error   = true;
            }

            if(!$validation_error){
                $token      = $request->input('token');
                if ($check_token = $answer_model->check_token($token, $course_id,$package_id)) {
                    $status     = "success";
                    $response   = "Token benar";
                }else{
                    $status     = "error";
                    $response   = "Token salah";
                }
            }
            $responses  = [
                "status"     => $status,
                "message"   => $response,
                "token"   => $token,
            ];
            echo json_encode($responses);
            exit;
        }

        $data["title"]          = "Token";
        $data["nav_active"]     = "exam";
        $data["course_id"]     = $course_id;
        $data["package_id"]     = $package_id;
        return view('exam.token',$data);
    }
}
