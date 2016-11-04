<?php

namespace App\Http\Controllers\Admin;

use App\EwordResult;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\Helper;
use App\EwordSession;

use DB;

class EwordController extends Controller
{
    public function index(Request $request) {
        $college_info = Helper::get_college_info();
        return view('admin/eword/index', [
            'college_info' => $college_info,
        ]);
    }

    public function list_by_date(Request $request) {
        $test_type = $request->input('test_type');
        $group_name = $request->input('group_name');

        // TODO(sonicmisora): Add group constraint
        $dates = EwordSession::by_date($test_type == 'with_wav', true);
        return view('admin/eword/list_by_date', [
            'test_type' => $test_type,
            'group_name' => $group_name,
            'dates' => $dates,
        ]);
    }

    public function detail(Request $request) {
        $test_type = $request->input('test_type');
        $group_name = $request->input('group_name');
        $date = $request->input('date');

        // TODO(sonicmisora): Add group constraint
        $results = EwordResult::select('session_id', 'is_correct', 'elapsed_time', 'quiz_index')
            ->whereIn('session_id', function ($query) use ($date, $test_type) {
                $query->selectRaw('id')
                    ->from('eword_sessions')
                    ->whereRaw('DATE(created_at) = ?', [$date])
                    ->where('with_wav', $test_type == 'with_wav')
                    ->where('is_test', true);
            })
            ->with(['session' => function($query) {
                $query->select('id', 'user_id');
            }, 'session.user' => function($query) {
                $query->select('id', 'username');
            }])
            ->get();
        // TODO(sonicmisora): make 'R' configurable
        $q_table = array_map('str_getcsv', file(public_path("upload/listening/setR/filelist.csv")));
        $overall_stat = $this->compute_stat($results, $q_table);

        return view('admin/eword/detail', [
            'date' => $date,
            'results' => $results,
            'overall_stat' => $overall_stat,
        ]);
    }

    // Private functions
    private function compute_stat($results, $q_table) {
        // Number of questions
        //$q_num = count($q_table);
        // Level for each question
        $qindex2level = [];
        // Number of questions for each level
        $nums_of_level = [];
        foreach ($q_table as $value) {
            $qindex2level[$value[0]] = $value[7];
            if (!key_exists($value[7], $nums_of_level)) {
                $nums_of_level[$value[7]] = 0;
            }
            $nums_of_level[$value[7]]++;
        }
        ksort($nums_of_level);

        // accumulate statistics
        $acc_stats = [];
        foreach ($results as $entry) {
            $username = $entry->session->user->username;
            if (!key_exists($username, $acc_stats)) {
                $acc_stats[$username] = [];
            }
            array_push($acc_stats[$username], [
                'i' => $entry->quiz_index,
                'c' => $entry->is_correct,
                'e' => $entry->elapsed_time,
            ]);
        }

        // Overal return table
        $ret = [
            'correct_rate' => [],
        ];
        // correct rate
        foreach ($acc_stats as $username => $stat) {
            $cr = $this->compute_stat_single($stat, 'c');
            $ret['correct_rate'][$username] = ['t' => $cr];
            foreach ($nums_of_level as $level => $num) {
                $level_stat = array_filter($stat, function($v) use ($qindex2level, $level) {
                    return $level == $qindex2level[$v['i']];
                });
                $cr = $this->compute_stat_single($level_stat, 'c');
                //var_dump($level);
                $ret['correct_rate'][$username][$level] = $cr;
            }
        }

        return $ret;
    }

    private function compute_stat_single($stat, $column) {
        $cnt = count($stat);
        if ($cnt == 0) {
            return 0.0;
        }
        $s = 0;
        foreach ($stat as $row) {
            $s += $row[$column];
        }
        return $s / $cnt;
    }
}
