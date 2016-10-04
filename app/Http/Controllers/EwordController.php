<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Log;

class EwordController extends Controller
{
    public function index(Request $request) {
        $with_wav = $request->input('with_wav') == 1;
        $is_test = $request->input('is_test') == 1;
        $college_info = [];
        if ($is_test) {
            // TODO: replace with real colleage list
            $college_info = [
                ["tiu", "logo/tiu.png"],
                ["aoyama", "logo/aoyama.gif"],
                ["chuo", "logo/chuo.gif"],
                ["seijo", "logo/seijo.gif"],
                ["u-tokyo", "logo/u-tokyo.gif"],
                #["tottori", "logo/tottori.gif"],
                ["kansai-u", "logo/kansai-u.gif"],
                ["zeneiren", "logo/zeneiren.png"],
                ["tokai", "logo/tokai.png"],
                ["guest", "guest"],
            ];
        }
        return view('eword/index', [
            'with_wav' => $with_wav,
            'is_test' => $is_test,
            'college_info' => $college_info,
        ]);
    }

    public function intro() {
        return view('');
    }

    public function quiz() {

    }

    public function result() {

    }

    public function summary() {

    }
}
