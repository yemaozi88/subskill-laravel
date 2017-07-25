<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Helper;
use App\Http\Controllers\Controller;
use App\RstResult;

class RstController extends Controller
{
    //
    public function index() {
        $college_info = Helper::get_college_info();
        $submit_url = action('Admin\RstController@list_by_date');
        return view('admin/memory/index', [
            'college_info' => $college_info,
            'submit_url' => $submit_url
        ]);
    }

    public function list_by_date(Request $request) {
        $group_name = $request->input('group_name');
        $title = "記憶クイズ(Reading)";
        $action_name = 'Admin\RstController@detail';
        $dates = RstResult::by_date('test', $group_name);
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
        $query_results = RstResult::select('user_id', 'last_word_list', 'judgement_list', 'correct_num', 'question_num')
            ->whereRaw('DATE(created_at) = ?', [$date])
            ->whereIn('user_id', function ($query) use ($group_name) {
                $query->select('id')
                    ->from('quiz_users')
                    ->where('group_name', $group_name);
            })->with(['user' => function($query) {
                $query->select('id', 'username');
            }])->get();
        $quiz_file = json_decode(file_get_contents(public_path('upload/rst/test/manifest.json')));
        $results = Helper::processResult($quiz_file->questionSets, $query_results);
        if ($format == "html") {
            return view('admin/memory/rst/detail', [
                'date' => $date,
                'group_name' => $group_name,
                'group_title' => $group_name,
                'results' => $results,
            ]);
        } else {
            $filename = "$date($group_name)(Reading).csv";
            $content = Helper::toCsv($results);
            return response($content)
                ->header('Content-Type', 'text/csv')
                ->header('charset', 'utf-8')
                ->header('Content-Disposition', "attachment; filename=$filename");
        }
    }
}
