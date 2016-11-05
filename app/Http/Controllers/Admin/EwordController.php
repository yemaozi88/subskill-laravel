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

        $dates = EwordSession::by_date($test_type == 'with_wav', true, $group_name);
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
        $format = $request->input('format');
        if ($format == "") {
            $format = "html";
        }

        $results = EwordResult::select('session_id', 'is_correct', 'elapsed_time', 'quiz_index')
            ->whereIn('session_id', function ($query) use ($date, $test_type, $group_name) {
                $query->selectRaw('id')
                    ->from('eword_sessions')
                    ->whereRaw('DATE(created_at) = ?', [$date])
                    ->where('with_wav', $test_type == 'with_wav')
                    ->where('is_test', true)
                    ->whereIn('user_id', function($query) use ($group_name) {
                        $query->select('id')
                            ->from('quiz_users')
                            ->where('group_name', $group_name);
                    });
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

        if ($format == "html") {
            return view('admin/eword/detail', [
                'date' => $date,
                'test_type' => $test_type,
                'group_name' => $group_name,
                'group_title' => $group_name,
                'overall_stat' => $overall_stat,
            ]);
        } else if ($format == "csv") {
            $filename = "$date.csv";
            $content = $this->stat2csv($overall_stat);
            return response($content)
                ->header('Content-Type', 'text/csv')
                ->header('charset', 'utf-8')
                ->header('Content-Disposition', "attachment; filename=$filename");
        } else {
            return "";
        }
    }

    // Private functions
    private function stat2csv($overall_stat) {
        $ret = "";
        $ret .= "Correct Rate[%]\n";
        $ret .= $this->gen_stat_table($overall_stat['correct_rate']);
        $ret .= "Reaction Time[Sec]\n";
        $ret .= $this->gen_stat_table($overall_stat['elapsed_time_mean']);
        $ret .= "Variance[Sec]\n";
        $ret .= $this->gen_stat_table($overall_stat['elapsed_time_var']);
        $ret .= "Stabability[Sec]\n";
        $ret .= $this->gen_stat_table($overall_stat['elapsed_time_stab']);
        return $ret;
    }

    private function gen_stat_table($stat) {
        if (count($stat) == 0) {
            return "";
        }
        $ret = "Name";
        foreach (reset($stat) as $key => $value) {
            $ret .= "," . ($key == 't' ? 'Overall' : "Level " . $key);
        }
        $ret .= "\n";
        foreach ($stat as $username => $entry) {
            $ret .= "$username";
            foreach ($entry as $key => $value) {
                $ret .= "," . number_format($value, 2);
            }
            $ret .= "\n";
        }
        return $ret;
    }

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
            'elapsed_time_mean' => [],
            'elapsed_time_var' => [],
            'elapsed_time_stab' => [],
        ];

        foreach ($acc_stats as $username => $stat) {
            // Statistics for is_correct, so the mean of it is correct rate
            $cr_res = $this->compute_stat_single($stat, 'c');
            $ret['correct_rate'][$username] = ['t' => $cr_res[0] * 100];
            // Statistics for elapsed time
            $et_res = $this->compute_stat_single($stat, 'e');
            $ret['elapsed_time_mean'][$username] = ['t' => $et_res[0]];
            $ret['elapsed_time_var'][$username] = ['t' => $et_res[1]];
            $ret['elapsed_time_stab'][$username] = ['t' => $et_res[2]];
            foreach ($nums_of_level as $level => $num) {
                $level_stat = array_filter($stat, function($v) use ($qindex2level, $level) {
                    return $level == $qindex2level[$v['i']];
                });
                $cr_res = $this->compute_stat_single($level_stat, 'c');
                $ret['correct_rate'][$username][$level] = $cr_res[0] * 100;
                $et_res = $this->compute_stat_single($level_stat, 'e');
                $ret['elapsed_time_mean'][$username][$level] = $et_res[0];
                $ret['elapsed_time_var'][$username][$level] = $et_res[1];
                $ret['elapsed_time_stab'][$username][$level] = $et_res[2];
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
        $mean = $s / $cnt;
        $var = 0;
        foreach ($stat as $row) {
            $var += ($row[$column] - $mean) * ($row[$column] - $mean);
        }
        $var = sqrt($var / $cnt);
        if ($mean == 0) {
            $stab = 0;
        } else {
            $stab = $var / $mean;
        }
        return [$mean, $var, $stab];
    }
}
