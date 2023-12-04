<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class ClassesModel
{
    public $table = 'classes';

    public function get_all()
    {
        $classes    = DB::table($this->table)
        ->get();
        return $classes;
    }
    public function get($class_id)
    {
        $class_obj    = DB::table($this->table)
        ->where('id',$class_id)
        ->first();
        return $class_obj;
    }
}
