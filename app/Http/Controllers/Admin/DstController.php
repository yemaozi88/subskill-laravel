<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DstResult;
use Helper;

class DstController extends Controller
{
    public function index() {
        $college_info = Helper::get_college_info();
        $submit_url = action('Admin\DstController@list_by_date');
        return view('admin/memory/index', [
            'college_info' => $college_info,
            'submit_url' => $submit_url
        ]);
    }

    public function list_by_date(Request $request) {
        $group_name = $request->input('group_name');
        $title = "数の記憶クイズ";
        $action_name = 'Admin\DstController@detail';
        $dates = DstResult::by_date('test', $group_name);
        return view('admin/memory/list_by_date', [
            'group_name' => $group_name,
            'action_name' => $action_name,
            'title' => $title,
            'dates' => $dates,
        ]);
    }

    public function detail(Request $request) {
        $group_name = $request->input('group_name');
        $date = $request->input('date');
        $format = $request->input('format');
        if ($format == "") {
            $format = "html";
        }
        $query_results = DstResult::select('user_id', 'user_digit_list', 'correct_digit_list', 'order_list', 'question_num')
            ->whereRaw('DATE(created_at) = ?', [$date])
            ->whereIn('user_id', function ($query) use ($group_name) {
                $query->select('id')
                    ->from('quiz_users')
                    ->where('group_name', $group_name);
            })->with(['user' => function($query) {
                $query->select('id', 'username');
            }])->get();
        $quiz_file = json_decode(file_get_contents(public_path('upload/dst/manifest.json')));
        $results = $this->processResult($quiz_file->questionSets->test, $query_results);
        if ($format == "html") {
            return view('admin/memory/dst/detail', [
                'date' => $date,
                'group_name' => $group_name,
                'group_title' => $group_name,
                'results' => $results,
            ]);
        } else {
            $filename = "$date($group_name)(Digit).csv";
            $content = $this->toCsv($results);
            return response($content)
                ->header('Content-Type', 'text/csv')
                ->header('charset', 'utf-8')
                ->header('Content-Disposition', "attachment; filename=$filename");
        }
    }

    public function processResult($question_sets, $results) {
        $set_question_num = count($question_sets);
        $ret = [];
        foreach ($results as $result) {
            $username = $result->user->username;
            $question_num = $result->question_num;
            $user_digit_list = $result->user_digit_list;
            $correct_digit_list =$result->correct_digit_list;
            $order_list = $result->order_list;
            if ($question_num != $set_question_num) {
                // There are some test data so these two numbers are not the same.
                // Just simply skip these test data.
                continue;
            }
            // The first element in these two arrays should be empty string
            // some thing like: "", "3,5", "4,2", ...
            $u_array = explode("#", $user_digit_list);
            // some thing like: "", "3,5", "4,2", ...
            $c_array = explode("#", $correct_digit_list);
            $index = 0;
            $user = ["username" => $username, "forward_best" => 0, "backward_best" => 0,"answers" => []];
            foreach ($question_sets as $question) {
                $answer = [];
                $index++;
                if ($u_array[$index] === $c_array[$index]) {
                    $answer["score"] = $question->num;
                } else {
                    $answer["score"] = 0;
                }
                $answer["right_list"] = $c_array[$index];
                $answer["input_list"] = $u_array[$index];
                // additional information
                $answer["order"] = $question->order;
                $answer["question_index"] = $index;
                $answer["header"] = $question->num.($question->order == "F" ? "a" : "b");
                array_push($user["answers"], $answer);
            }
            $user["forward_best"] = array_reduce($user["answers"], function($c, $x) {
                if ($x["order"] == 'F') { return max($c, $x["score"]); }
                return $c;
            }, 0);
            $user["backward_best"] = array_reduce($user["answers"], function($c, $x) {
                if ($x["order"] == 'B') { return max($c, $x["score"]); }
                return $c;
            }, 0);
            array_push($ret, $user);
        }
        return $ret;
    }

    public function toCsv($results) {
        $ret = '';
        foreach ($results as $user) {
            $ret .= $user["username"].",,";
            foreach ($user["answers"] as $answer) {
                $ret .= '"'.$answer["input_list"].'",';
                $ret .= '"'.$answer["right_list"].'",';
                $ret .= $answer["score"].',';
            }
            $ret .= $user["forward_best"].",";
            $ret .= $user["backward_best"]."\n";
        }
        return $ret;
    }
}
