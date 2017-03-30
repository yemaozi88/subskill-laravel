<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;

class RstController extends Controller
{
    public function index(Request $request)
    {
        $is_test = $request->input("is_test") == 1;
        $college_info = [];
        if ($is_test) {
            $college_info = Helper::get_college_info();
        }
        return view("rst/index", [
            'is_test' => $is_test,
            'college_info' => $college_info,
        ]);
    }

    public function quiz(Request $request) {
        $is_test = $request->input('is_test') == 1;
        $username = $request->input('username');
        $group_name = $request->input('group_name');
        $quiz_set_name = $request->input('is_test') == 1 ? 'test' : 'practice';
        $show_answer = !$is_test;
        $q_set = $this->parse_manifest(public_path('upload/rst/'.$quiz_set_name.'/manifest.json'));
        $manifest_url = url('upload/rst/'.$quiz_set_name.'/manifest.json');
        $audio_folder_url = url('upload/rst/'.$quiz_set_name);
        $send_answer_url = url('api/rst/create');
        $q_num = count($q_set);

        return view("rst/quiz", [
            'is_test' => $is_test,
            'q_num' => $q_num,
            'username' => $username,
            'group_name' => $group_name,
            'manifest_url' => $manifest_url,
            'audio_folder_url' => $audio_folder_url,
            'send_answer_url' => $send_answer_url,
            'quiz_set_name' => $quiz_set_name,
            'show_answer' => $show_answer,
        ]);
    }

    private function parse_manifest($filepath) {
        $ret = json_decode(file_get_contents($filepath));
        return $ret->questionSets;
    }
}
