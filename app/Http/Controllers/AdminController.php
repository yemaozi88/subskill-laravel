<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Helpers\Helper;

class AdminController extends Controller
{
    public function index(Request $request) {
        return view('admin/index');
    }

    public function eword(Request $request) {
        $college_info = Helper::get_college_info();
        return view('admin/eword', [
            'college_info' => $college_info,
        ]);
    }

    public function eword_list(Request $request) {
        return view('admin/eword_list');
    }
}
