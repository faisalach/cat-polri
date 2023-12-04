<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use \App\Models\CourseModel;
use \App\Models\UserModel;

class AnswerModel
{
    public $table = 'answers';
    public function insert($answer_arr)
    {
        $data_insert    = [];
        foreach($answer_arr as $question_id => $answer){
            $data   = [
                "user_id"   => session('user_id'),
                "question_id" => $question_id,
                "answer"    => is_array($answer) ? json_encode($answer) : $answer,
            ];
            $data_insert[]  = $data;
        }
        $insert    = DB::table($this->table)
        ->insert($data_insert);
        return $insert;
    }
    public function count_score($answer_arr, $course_id)
    {
        $course_model   = new CourseModel();
        $course_obj     = $course_model->get($course_id);
        $course_category_id     = $course_obj->course_category_id;
        $questions      = DB::table('questions')->where('course_id',$course_id)->get();
        $questions_arr    = [];
        foreach($questions as $question_obj){
            $questions_arr[$question_obj->id] = $question_obj;
        }

        switch($course_category_id){
            case 1:
            case 2:
            $count_true    = [];
            $count_course_by_sub_category_arr = [];
            foreach($answer_arr as $question_id => $answer){
                if (!empty($questions_arr[$question_id])) {
                    $question_obj   = $questions_arr[$question_id];

                    if (!isset($count_course_by_sub_category_arr[$question_obj->course_sub_category_id])) {
                        $count_course_by_sub_category_arr[$question_obj->course_sub_category_id] = 0;
                    }

                    $count_course_by_sub_category_arr[$question_obj->course_sub_category_id]++;

                    if (!isset($count_true[$question_obj->course_sub_category_id])) {
                        $count_true[$question_obj->course_sub_category_id] = 0;
                    }

                    if ($question_obj->choice_type == 'hybrid') {
                        $isCorrect      = false;
                        if (is_array($answer)) {
                            $answer_arr         = $answer;
                            $correct_choice_arr = [$question_obj->correct_choice,$question_obj->correct_choice_2];
                            if (in_array((!empty($answer_arr[0]) ? $answer_arr[0] : ''),$correct_choice_arr)) {
                                if (in_array((!empty($answer_arr[1]) ? $answer_arr[1] : ''),$correct_choice_arr)) {
                                    $isCorrect = true;
                                }
                            }
                        }
                        if ($isCorrect) {
                            $count_true[$question_obj->course_sub_category_id]++;
                        }
                    }else{
                        if ($question_obj->correct_choice == $answer) {
                            $count_true[$question_obj->course_sub_category_id]++;
                        }
                    }

                }
            }
            $score    = [];
            foreach($count_true as $csc_id => $true){
                $score[$csc_id]     = count_score($true,$count_course_by_sub_category_arr[$csc_id]);
            }
            return $score;
            break;
            case 3:
            $total_questions        = $course_obj->number_of_questions;
            $total_answer           = 0;
            $total_correct_answer   = 0;
            $total_answer_per_column    = [];

            for ($i=0; $i < count_course(3)[0]; $i++) {
                $total_answer_per_column[$i]    = 0;
            }

            foreach($answer_arr as $question_id => $answer){
                if (!empty($questions_arr[$question_id])) {
                    if (!empty($answer)) {
                        $total_answer++;

                        $question_obj   = $questions_arr[$question_id];
                        $number     = $question_obj->number - 1;
                        $column     = floor($number / count_course(3)[1]);
                        $total_answer_per_column[$column]++;

                        if ($question_obj->correct_choice == $answer) {
                            $total_correct_answer++;
                        }
                    }
                }

            }

                // Kecepatan
            $score[12]      = count_score($total_answer,$total_questions);
                // Ketelitian
            $score[13]      = count_score($total_correct_answer,$total_answer);
                // Ketahanan
            $score[14]      = linear_regression(array_keys($total_answer_per_column),array_values($total_answer_per_column));

            return $score;
            break;
            default:
            $true   = 0;
            $number_of_questions = $course_obj->number_of_questions;
            foreach($answer_arr as $question_id => $answer){
                if (!empty($questions_arr[$question_id])) {
                    $question_obj   = $questions_arr[$question_id];
                    if ($question_obj->correct_choice == $answer) {
                        $true++;
                    }
                }
            }
            if ($true > $number_of_questions) {
                $true   = $number_of_questions;
            }
            $sub_categories = $course_model->get_course_sub_categpry($course_category_id);
            $course_sub_category_id = !empty($sub_categories[0]->id) ? $sub_categories[0]->id : 0;
            $score[$course_sub_category_id]  = count_score($true,$number_of_questions);

            return $score;
            break;
        }
    }

    public function save_score($course_id,$course_sub_category_id,$score,$course_package_id)
    {
        
        $user_id        = session('user_id');
        // get data scores by user_id, course_id and empty course_sub_category_id
        $score_obj      = DB::table('scores')
        ->where('user_id',$user_id)
        ->where('course_id',$course_id)
        ->where(DB::raw(" (SELECT COUNT(1) as total FROM detail_scores WHERE scores.id = detail_scores.score_id AND course_sub_category_id = '$course_sub_category_id' )  "),0)
        ->first();
        
        $course_model   = new CourseModel();
        $course_obj     = $course_model->get($course_id);
        $course_type_id      = !empty($course_obj->course_type_id) ? $course_obj->course_type_id : "";
        
        if (!empty($score_obj->id)) {
            $score_id   = $score_obj->id;
        }else{
            $data_insert    = [
                "user_id" => $user_id,
                "course_id" => $course_id,
                "course_package_id" => $course_package_id,
            ];
            $score_id   = DB::table('scores')->insertGetId($data_insert);
        }

        if (!empty($score_id)) {
            $data_insert_detail     = [
                "score_id"                  => $score_id,
                "course_sub_category_id"    => $course_sub_category_id,
                "score"                     => $score,
                "course_type_id"            => $course_type_id
            ];
            $detail_score_id   = DB::table('detail_scores')->insertGetId($data_insert_detail);
            update_final_score($course_type_id,$user_id,$course_package_id);
            return true;
        }else{
            return false;
        }

    }

    public function getScores($user_id,$course_type_id,$course_package_id)
    {
        $user_model     = new UserModel();
        $user_obj       = $user_model->get($user_id);

        $query     = DB::table('scores');
        $query->select("scores.id","course_category.name as course_category_name","course_category.id as course_category_id");
        $query->rightJoin("courses",'courses.id','=','scores.course_id');
        $query->join("course_category",'course_category.id','=','courses.course_category_id');
        // $query->join("course_to_packages",'course_to_packages.course_id','=','scores.course_id');
        $query->where('course_package_id',$course_package_id);

        if ($user_obj->user_group_id == 3 && $course_type_id != 1) {
            $query->leftJoin("course_classes","course_classes.course_id","=","courses.id");
            $query->where(function($query) use ($user_obj)
            {
                $query->where('class_id',null)->orWhere('class_id',$user_obj->class_id);
            });
        }

        $query->where(function($query) use ($user_id)
        {
            $query->where('scores.user_id',$user_id);
            $query->orWhere('scores.user_id',null);
        });
        $query->where('courses.course_type_id',$course_type_id);
        if($course_type_id == 1){
            $query->orderBy(DB::raw('FIELD(course_category_name, "Kecerdasan", "Kepribadian", "Kecermatan")'),'ASC');
        }else{
            $query->orderBy('courses.id','ASC');
        }
        $scores     = $query->get();


        $scores_arr     = [];
        $final_score    = 0;
        $isFinish       = 1;
        foreach($scores as $score_obj){
            $score_arr      = $this->getScore($score_obj->id,$course_type_id);
            $data       = [
                "category_name" => $score_obj->course_category_name,
                "data"          => $score_arr
            ];

            foreach($score_arr as $key => $score){
                if (!isset($score->score)) {
                    $isFinish   = false;
                }
                if ($course_type_id == 1) {
                    $numeric    = score_convert("numeric",$score->result);
                    $count_score_final  = count_score_final($score_obj->course_category_id,$score->sub_category_id,$numeric);
                    $final_score    = $final_score + $count_score_final;
                }else{
                    $count_score_final  = count_score_final($score_obj->course_category_id,$score->sub_category_id,$score->score);
                    $final_score    = $final_score + $count_score_final;
                }
            }

            $scores_arr["score"][] = $data;
        }

        $courses    = DB::table('courses')->select('courses.id')
        ->join('course_to_packages','course_to_packages.course_id','=','courses.id')
        ->where('course_to_packages.course_package_id',$course_package_id)
        ->where('course_to_packages.course_type_id',$course_type_id)
        ->get();
        foreach($courses as $course ){
            if (!isAnswered($course->id,$user_id,$course_package_id)) {
                $isFinish = false;
            }
        }

        if (!$isFinish) {
            $final_score    = 0;
        }
        if ($final_score < 30) {
            $final_score    = 30;
        }

        $scores_arr["finish"] = $isFinish;
        $scores_arr["final_score"] = round($final_score,2);
        $scores_arr["description"] = score_convert("final_score",$final_score);

        return $scores_arr;
    }
    public function getScore($score_id,$course_type_id)
    {
        $query  = DB::table("detail_scores");
        $query->select("detail_scores.score","course_sub_category.name as sub_category_name","course_sub_category.id as sub_category_id","detail_scores.score_id");
        $query->rightJoin("course_sub_category","course_sub_category.id" ,"=" , "detail_scores.course_sub_category_id");
        $query->where("score_id",$score_id);
        $query->orderBy("sub_category_id",'asc');
        $detail_scores = $query->get();
        $detail_scores_arr  = [];
        foreach($detail_scores as $detail_score){
            if(!isset($detail_score->score)){
                $detail_score->result = "";
            }elseif ($course_type_id == 1) {
                $detail_score->result    = score_convert("alphabet", $detail_score->score);
            }else{
                $detail_score->result    = round($detail_score->score,2);
            }
            $detail_scores_arr[]    = $detail_score;
        }

        return $detail_scores_arr;
    }
    public function check_token($token, $course_id,$course_package_id)
    {
        $check  = DB::table('token_test')
        ->where('token_key',$token)
        ->where('course_id',$course_id)
        ->where('course_package_id',$course_package_id)
        ->where('status',1)
        ->first();
        return !empty($check);
    }
}
