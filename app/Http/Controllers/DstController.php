<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\QuizUser;
use App\DstResult;

class DstController extends Controller
{
    public function index(Request $request)
    {
        $is_test = $request->input("is_test") == 1;
        $college_info = [];
        if ($is_test) {
            $college_info = Helper::get_college_info();
        }
        return view("dst/index", [
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
        $raw_data = $this->parse_manifest(public_path('upload/dst/manifest.json'));
        $manifest_url = url('upload/dst/manifest.json');
        $send_answer_url = url('api/dst/create');
        $q_num = count($raw_data["questionSets"][$quiz_set_name]);

        return view("dst/quiz", [
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
        $ret = json_decode(file_get_contents($filepath), true);
        return $ret;
    }
    public function create(Request $request) {
        $username = $request->input('username');
        $group_name = $request->input('group_name');
        if ($username && $group_name) {
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
                'reason' => 'Username and group name must be provided',
            ]);
        }
        $info = [
            'user_id' => $user_id,
            'quiz_set_name' => $request->input('quiz_set_name'),
            'correct_num' => $request->input('correct_num'),
            'question_num' => $request->input('question_num'),
            'user_digit_list' => $request->input('user_digit_list'),
            'correct_digit_list' => $request->input('correct_digit_list'),
            'order_list' => $request->input('order_list'),
        ];
        $model = DstResult::create($info);
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
