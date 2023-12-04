<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class ScoreModel
{
    public $table = 'scores';
    public function delete($id)
    {
        $delete    = DB::table($this->table)->where('id',$id)->delete();
        return $delete;
    }
    public function get_by($where)
    {
        $score    = DB::table($this->table)
        ->where($where)
        ->first();
        return $score;
    }
    public function get($score_id)
    {
        $score    = DB::table($this->table)
        ->where('id',$score_id)
        ->first();
        return $score;
    }
    public function get_scores()
    {
        $score    = DB::table('users')
        ->select('users.id',DB::raw("classes.name as class_name"),DB::raw("CONCAT(first_name,' ',last_name) as name"))
        ->join("classes","classes.id","=","users.class_id")
        ->where("users.user_group_id",3)
        ->where(DB::raw(" (SELECT COUNT(1) FROM scores WHERE users.id = scores.user_id) "),'>',0)
        ->get();
        return $score;
    }
}
