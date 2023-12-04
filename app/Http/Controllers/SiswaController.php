<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function dashboard(Request $request)
    {
        return redirect(route('exam.list'));
        $data["title"]          = "Dashboard";
        $data["nav_active"]     = "dashboard";
        return view('siswa.dashboard',$data);
    }
}
