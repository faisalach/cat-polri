<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class QuestionModel
{
    public $table = 'questions';
    public function insert($insert_datas)
    {
        $DIR_UPLOAD     = global_settings("DIR_UPLOAD");

        foreach($insert_datas as $insert_data){
            $files      = !empty($insert_data["files"]) ? $insert_data["files"] : [];
            $files_arr  = !empty($insert_data["files_ready"]) ? $insert_data["files_ready"] : [];
            if (!empty($files)) {
                foreach($files as $key => $file){
                    $path       = pathinfo($file["name"]);
                    $extension  = $path["extension"];
                    $newName    = "question_".$insert_data["number"]."_".$key."_".time().".".$extension;
                    $raw        = base64_decode($file["file"]);
                    $save       = file_put_contents(global_settings("PUBLIC_ROOT")."/$DIR_UPLOAD/$newName", $raw);
                    if ($save) {
                        $files_arr[]    = $newName;
                    }else{
                        return false;
                    }
                }
            }

            if (is_array($files_arr)) {
                $files_arr  = json_encode($files_arr);
            }

            $data_insert_questions  = [
                "course_sub_category_id"    => !empty($insert_data["course_sub_category_id"]) ? $insert_data["course_sub_category_id"] : 0,
                "course_id"    => $insert_data["course_id"],
                "question"    => $insert_data["question"],
                "number"    => $insert_data["number"],
                "choice_type"    => !empty($insert_data["choice_type"]) ? $insert_data["choice_type"] : "normal",
                "correct_choice"    => $insert_data["correct_choice"],
                "correct_choice_2"    => !empty($insert_data["correct_choice_2"]) ? $insert_data["correct_choice_2"] : "",
                "files"    => $files_arr,
            ];

            $question_id   = DB::table($this->table)->insertGetId($data_insert_questions);
            if ($question_id) {
                $data_insert_choices    = [];
                foreach ($insert_data["choices"] as $choice => $title) {
                    $data_insert_choices[]    = [
                        "question_id"   => $question_id,
                        "choice"        => $choice,
                        "title"         => $title,
                    ];
                }
                $insert_choices  = DB::table("multiple_choice")->insert($data_insert_choices);
            }else{
                return false;
            }
        }

        return true;
    }

    public function update($update_datas)
    {
        $DIR_UPLOAD     = global_settings("DIR_UPLOAD");
        if (empty($update_datas)) {
            return false;
        }
        foreach($update_datas as $update_data){
            $files      = !empty($update_data["files"]) ? $update_data["files"] : [];
            $files_arr  = !empty($update_data["files_ready"]) ? $update_data["files_ready"] : [];
            if (!empty($files)) {
                foreach($files as $key => $file){
                    $path       = pathinfo($file["name"]);
                    $extension  = $path["extension"];
                    $newName    = "question_".$update_data["number"]."_".$key."_".time().".".$extension;
                    $raw        = base64_decode($file["file"]);
                    $save       = file_put_contents(global_settings("PUBLIC_ROOT")."/$DIR_UPLOAD/$newName", $raw);
                    if ($save) {
                        $files_arr[]    = $newName;
                    }else{
                        return false;
                    }
                }
            }

            if (is_array($files_arr)) {
                $files_arr  = json_encode($files_arr);
            }

            $where_get_questions  = [
                "course_id"    => $update_data["course_id"],
                "number"    => $update_data["number"],
            ];

            $question_obj   = DB::table($this->table)->where($where_get_questions)->first();
            if (!empty($question_obj)) {
                $data_update_questions  = [
                    "course_sub_category_id"    => !empty($update_data["course_sub_category_id"]) ? $update_data["course_sub_category_id"] : 0,
                    "question"    => $update_data["question"],
                    "choice_type"    => !empty($update_data["choice_type"]) ? $update_data["choice_type"] : "normal",
                    "correct_choice"    => $update_data["correct_choice"],
                    "correct_choice_2"    => !empty($update_data["correct_choice_2"]) ? $update_data["correct_choice_2"] : "",
                    "files"    => $files_arr,
                ];
                $update   = DB::table($this->table)->where('id',$question_obj->id)->update($data_update_questions);
                $question_id    = $question_obj->id;
            }else{
                $data_insert_questions  = [
                    "course_sub_category_id"    => !empty($update_data["course_sub_category_id"]) ? $update_data["course_sub_category_id"] : 0,
                    "course_id"    => $update_data["course_id"],
                    "question"    => $update_data["question"],
                    "number"    => $update_data["number"],
                    "choice_type"    => !empty($update_data["choice_type"]) ? $update_data["choice_type"] : "normal",
                    "correct_choice"    => $update_data["correct_choice"],
                    "correct_choice_2"    => !empty($update_data["correct_choice_2"]) ? $update_data["correct_choice_2"] : "",
                    "files"    => $files_arr,
                ];

                $question_id   = DB::table($this->table)->insertGetId($data_insert_questions);
            }
            if ($question_id) {
                $data_insert_choices    = [];
                foreach ($update_data["choices"] as $choice => $title) {
                    $where_update_choices    = [
                        "question_id"   => $question_id,
                        "choice"        => $choice,
                    ];
                    $choices_obj  = DB::table("multiple_choice")->where($where_update_choices)->first();
                    if (!empty($choices_obj)) {
                        $data_update_choices    = [
                            "title"         => $title,
                        ];
                        $update_choices  = DB::table("multiple_choice")->where($where_update_choices)->update($data_update_choices);
                    }else{
                        $data_insert_choices[]    = [
                            "question_id"   => $question_id,
                            "choice"        => $choice,
                            "title"         => $title,
                        ];
                    }
                }
                $insert_choices  = DB::table("multiple_choice")->insert($data_insert_choices);
            }else{
                return false;
            }
        }

        return true;
    }

    public function get_question($course_id)
    {
        $DIR_UPLOAD     = global_settings("DIR_UPLOAD");
        $isRandom    = DB::table('courses')
        ->select('course_category.isRandom')
        ->join("course_category","course_category.id","=","courses.course_category_id")
        ->where('courses.id',$course_id)
        ->value('isRandom');


        $query    = DB::table($this->table)
        ->select("questions.*",DB::raw("course_sub_category.name as course_sub_category_name"))
        ->leftJoin("course_sub_category","course_sub_category.id","=","questions.course_sub_category_id")
        ->where("course_id",$course_id);
        if (!empty($isRandom)) {
            $query->inRandomOrder();
        }
        $questions  = $query->get();



        if (empty($questions[0])) {
            return [];
        }
        $questions_arr  = [];
        foreach($questions as $key => $question){
            $choices    = DB::table("multiple_choice")
            ->where('question_id',$question->id)
            ->get();

            $choices_arr    = [];
            foreach($choices as $choice){
                $choices_arr[$choice->choice] = $choice->title;
            }

            $question->choices = $choices_arr;

            $files          = $question->files;
            $files          = json_decode($files);
            if (!empty($files)) {
                foreach ($files as $k => $file) {
                    $files[$k]  = url($DIR_UPLOAD)."/".$file;
                }
            }
            $question->files  = $files;
            $questions_arr[$question->number - 1]    = $question;
        }
        return $questions_arr;
    }
    public function get_question_for_exam($course_id)
    {
        $DIR_UPLOAD     = global_settings("DIR_UPLOAD");
        $isRandom    = DB::table('courses')
        ->select('course_category.isRandom')
        ->join("course_category","course_category.id","=","courses.course_category_id")
        ->where('courses.id',$course_id)
        ->value('isRandom');


        $query    = DB::table($this->table)
        ->select("questions.*",DB::raw("course_sub_category.name as course_sub_category_name"))
        ->leftJoin("course_sub_category","course_sub_category.id","=","questions.course_sub_category_id")
        ->where("course_id",$course_id);
        if (!empty($isRandom)) {
            $query->inRandomOrder();
        }
        $questions  = $query->get();

        

        if (empty($questions[0])) {
            return [];
        }
        $questions_arr  = [];
        foreach($questions as $key => $question){
            $choices    = DB::table("multiple_choice")
            ->where('question_id',$question->id)
            ->get();

            $choices_arr    = [];
            foreach($choices as $choice){
                $choices_arr[$choice->choice] = $choice->title;
            }

            $question->choices = $choices_arr;

            $files          = $question->files;
            $files          = json_decode($files);
            if (!empty($files)) {
                foreach ($files as $k => $file) {
                    $files[$k]  = url($DIR_UPLOAD)."/".$file;
                }
            }
            $question->files  = $files;
            if ($isRandom) {
                $questions_arr[]    = $question;
            }else{
                $questions_arr[$question->number - 1]    = $question;
            }
        }
        return $questions_arr;
    }
}
