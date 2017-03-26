<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\Helper;

class LstController extends Controller
{
    public function index(Request $request) {
        $is_test = $request->input('is_test') == 1;
        $college_info = [];
        if ($is_test) {
            $college_info = Helper::get_college_info();
        }
        return view('lst/index', [
            'is_test' => $is_test,
            'college_info' => $college_info,
        ]);
    }

    
    public function quiz(Request $request) {
        $is_test = $request->input('is_test') == 1;
        $q_size = $request->input('q_size');
        $username = $request->input('username');
        $q_num = 0;
        return view('lst/quiz', [
            'is_test' => $is_test,
            'q_num' => $q_num,
            'username' => $username,
        ]);
    }
}
