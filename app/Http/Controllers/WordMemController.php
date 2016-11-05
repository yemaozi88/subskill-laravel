<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\Helper;

class WordMemController extends Controller
{
    public function index(Request $request) {
        $is_test = $request->input('is_test') == 1;
        $college_info = [];
        if ($is_test) {
            $college_info = Helper::get_college_info();
        }
        return view('word_mem/index', [
            'is_test' => $is_test,
            'college_info' => $college_info,
        ]);
    }
    
    public function quiz(Request $request) {
        $is_test = $request->input('is_test') == 1;
        return view('word_mem/quiz', [
            'is_test' => $is_test,
        ]);
    }
}
