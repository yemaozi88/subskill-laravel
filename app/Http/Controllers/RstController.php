<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\QuizUser;
use App\RstResult;

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
        $show_answer = !$is_test ? "true" : "false";
        $q_set = $this->parse_manifest(public_path('upload/rst/'.$quiz_set_name.'/manifest.json'));
        $manifest_url = url('upload/rst/'.$quiz_set_name.'/manifest.json');
        $send_answer_url = url('api/rst/create');
        $q_num = count($q_set);

        return view("rst/quiz", [
            'is_test' => $is_test,
            'q_num' => $q_num,
            'username' => $username,
            'group_name' => $group_name,
            'manifest_url' => $manifest_url,
            'send_answer_url' => $send_answer_url,
            'quiz_set_name' => $quiz_set_name,
            'show_answer' => $show_answer,
        ]);
    }

    private function parse_manifest($filepath) {
        $ret = json_decode(file_get_contents($filepath));
        return $ret->questionSets;
    }

    public function create(Request $request) {
        $username = $request->input('username');
        $group_name = $request->input('group_name');
        if ($username) {
            $user = QuizUser::where(['username' => $username, 'group_name' => $group_name])->first();
            if ($user == null) {
                $user = QuizUser::create([
                    'username' => $username,
                    'group_name' => $group_name,
                ]);
                if ($user == null) {
                    return response()->json([
                        'ret' => 'failed',
                        'reason' => 'Failed to create user.',
                    ]);
                }
            }
            $user_id = $user->id;
        } else {
            return response()->json([
                'ret' => 'failed',
                'reason' => 'Username must be provided',
            ]);
        }
        $info = [
            'user_id' => $user_id,
            'quiz_set_name' => $request->input('quiz_set_name'),
            'correct_num' => $request->input('correct_num'),
            'question_num' => $request->input('question_num'),
            'last_word_list' => $request->input('last_word_list'),
            'judgement_list' => $request->input('judgement_list'),
        ];
        $model = RstResult::create($info);
        if ($model == null) {
            return response()->json([
                'ret' => 'failed',
                'reason' => 'Failed to create record.',
            ]);
        }
        return response()->json([
            'ret' => 'ok',
        ]);
    }
}
