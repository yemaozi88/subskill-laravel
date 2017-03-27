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
        $username = $request->input('username');
        // TODO(sonicmisora): add test manifest here
        $q_set = $this->parse_manifest(public_path('upload/lst/practice/manifest.json'));
        $manifest_url = url('upload/lst/practice/manifest.json');
        $audio_folder_url = url('upload/lst/practice');

        $q_num = count($q_set);
        //var_dump($q_set);
        return view('lst/quiz', [
            'is_test' => $is_test,
            'q_num' => $q_num,
            'username' => $username,
            'manifest_url' => $manifest_url,
            'audio_folder_url' => $audio_folder_url,
        ]);
    }

    private function parse_manifest($filepath) {
        $ret = json_decode(file_get_contents($filepath));
        return $ret->questionSets;
    }
}
