<?php

namespace App\Http\Controllers;

use App\EwordResult;
use App\EwordSession;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Http\Requests;
use App\QuizUser;

use URL;
use DB;

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

    public function intro(Request $request) {
        $with_wav = $request->input('with_wav') == 1;
        $is_test = $request->input('is_test') == 1;
        $group_name = $request->input('group_name');
        $username = $request->input('username');
        $q_set = $request->input('q_set');

        $set_dir = public_path("upload/listening/set$q_set");
        $q = array_map('str_getcsv', file("$set_dir/filelist.csv"));
        $q_num = count($q);

        $user = QuizUser::where('username', $username)
            ->where('group_name', $group_name)
            ->first();
        if ($user === null) {
            $user = new QuizUser;
            $user->username = $username;
            $user->group_name = $group_name;
            $user->save();
        }
        $user_id = $user->id;

        $session = EwordSession::where([
            'user_id' => $user_id,
            'with_wav' => $with_wav,
            'is_test' => $is_test,
            'quiz_set' => $q_set,
        ])->first();
        if ($session === null || $session->finished) {
            # If no session found or last session is already finished,
            # create a new one
            $session = EwordSession::create([
                'user_id' => $user_id,
                'with_wav' => $with_wav,
                'is_test' => $is_test,
                'quiz_set' => $q_set,
            ]);
        }
        $session_id = $session->id;

        return view('eword/intro', [
            "username" => $username,
            "user_id" => $user_id,
            "q_set" => $q_set,
            "session_id" => $session_id,
            "q_num" => $q_num,
            "with_wav" => $with_wav,
            "is_test" => $is_test,
        ]);
    }

    public function quiz(Request $request) {
        $with_wav = $request->input('with_wav') == 1;
        $is_test = $request->input('is_test') == 1;
        $user_id = $request->input("user_id");
        $username = $request->input('username');
        $q_set = $request->input('q_set');
        $session_id = $request->input('session_id');

        $set_dir = public_path("upload/listening/set$q_set");
        $set_url = URL::asset("upload/listening/set$q_set");
        $q = array_map('str_getcsv', file("$set_dir/filelist.csv"));
        $q_num = count($q);

        $info = DB::table('eword_results')->selectRaw('MAX(quiz_index) as quiz_index,
            SUM(is_correct) as correct_num')->where('session_id', $session_id)->first();
        if ($info === null) {
            $finished_quiz_index = -1;
            $correct_num = 0;
        } else {
            $finished_quiz_index = $info->quiz_index;
            $correct_num = $info->correct_num;
        }

        if ($finished_quiz_index == -1) {
            // If this is the first time user takes this test,
            // or he already finished last test, start a new one.
            $q_index = 1;
        } elseif ($finished_quiz_index < $q_num) {
            // Last test hasn't been finished. Continue from last question.
            $q_index = $finished_quiz_index + 1;
        } else {
            // TODO: should goto result page here.
            $q_index = -1;
        }

        $wav_path = $set_url . "/$q_index.wav";
        $mp3_path = $set_url . "/$q_index.mp3";
        $q_data = null;
        foreach ($q as $index => $line) {
            if ($line[0] == $q_index) {
                $q_data = [];
                for ($i = 1; $i < 1 + 6; $i++) {
                    array_push($q_data, mb_convert_encoding($line[$i], "UTF-8", "auto"));
                }
                break;
            }
        }
        if ($q_data === null) {
            throw new NotFoundHttpException('No such question index. Please contact admin.');
        }
        return view('eword/quiz', [
            "with_wav" => $with_wav,
            "is_test" => $is_test,
            "username" => $username,
            "user_id" => $user_id,
            "q_num" => $q_num,
            "q_data" => $q_data,
            "q_set" => $q_set,
            "q_index" => $q_index,
            "correct_num" => $correct_num,
            "wav_path" => $wav_path,
            "mp3_path" => $mp3_path,
            "session_id" => $session_id,
        ]);
    }

    public function result(Request $request) {
        $username = $request->input("username");
        $user_id = $request->input("user_id");
        $q_set = $request->input("q_set");
        $q_selection = $request->input("q_selection");
        $q_index = $request->input("q_index");
        $play_count = $request->input("play_count");
        $elapsed_time = $request->input("elapsed_time");
        $session_id = $request->input("session_id");
        $with_wav = $request->input("with_wav") == 1;
        $is_test = $request->input("is_test") == 1;

        $set_dir = public_path("upload/listening/set$q_set");
        $q = array_map('str_getcsv', file("$set_dir/filelist.csv"));
        $q_num = count($q);
        $q_data = null;
        foreach ($q as $line) {
            if ($line[0] == $q_index) {
                $q_data = [];
                for ($i = 1; $i < 1 + 6; $i++) {
                    array_push($q_data, mb_convert_encoding($line[$i], "UTF-8", "auto"));
                }
                break;
            }
        }
        if ($q_data === null) {
            throw new NotFoundHttpException('No such question index. Please contact admin.');
        }
        $is_correct = $q_data[5] == $q_selection;
        $is_blank_answer = $q_selection == 0;
        // TODO: do index check here.
        EwordResult::create([
            "session_id" => $session_id,
            "play_count" => $play_count,
            "user_answer" => $q_selection,
            "is_correct" => $is_correct,
            "quiz_index" => $q_index,
            "elapsed_time" => $elapsed_time
        ]);

        $is_last = $q_num <= $q_index;
        if ($is_last) {
            DB::table('eword_sessions')->where('id', $session_id)->update(['finished' => true]);
        }
        $is_redirect = $is_test;

        return view('eword/result', [
            "with_wav" => $with_wav,
            "is_test" => $is_test,
            "is_last" => $is_last,
            "username" => $username,
            "q_num" => $q_num,
            "q_data" => $q_data,
            "q_set" => $q_set,
            "q_index" => $q_index,
            "is_correct" => $is_correct,
            "user_id" => $user_id,
            "session_id" => $session_id,
            "elapsed_time" => $elapsed_time,
            "is_redirect" => $is_redirect,
            "is_blank_answer" => $is_blank_answer,
        ]);
    }

    public function summary(Request $request) {
        $username = $request->input("username");
        $user_id = $request->input("user_id");
        $q_set = $request->input("q_set");
        $session_id = $request->input("session_id");
        $with_wav = $request->input("with_wav") == 1;
        $is_test = $request->input("is_test") == 1;

        $set_dir = public_path("upload/listening/set$q_set");
        $q = array_map('str_getcsv', file("$set_dir/filelist.csv"));
        $q_num = count($q);
        $q_map = [];
        $nums_perlevel = [];
        foreach ($q as $value) {
            $q_map[$value[0]] = $value[7];
            if (!key_exists($value[7], $nums_perlevel)) {
                $nums_perlevel[$value[7]] = 0;
            }
            $nums_perlevel[$value[7]]++;
        }
        $info = EwordResult::where('session_id', $session_id)->get();
        $results = [];
        foreach ($info as $row) {
            $level = $q_map[$row->quiz_index];
            $t = ["level" => $level, "q_index" => $row->quiz_index,
                "elapsed_time" => $row->elapsed_time,
                "is_correct" => $row->is_correct];
            array_push($results, $t);
        }

        $total_data = $this->generate_data_for_table($results, $nums_perlevel);
        $correct_data = $this->generate_data_for_table(array_filter($results, function ($v) {
            return $v["is_correct"] == true;
        }), $nums_perlevel);
        $wrong_data = $this->generate_data_for_table(array_filter($results, function ($v) {
            return $v["is_correct"] == false;
        }), $nums_perlevel);

        return view('eword/summary', [
            "with_wav" => $with_wav,
            "is_test" => $is_test,
            "username" => $username,
            "q_num" => $q_num,
            "q_set" => $q_set,
            "user_id" => $user_id,
            "session_id" => $session_id,
            "total_data" => $total_data,
            "correct_data" => $correct_data,
            "wrong_data" => $wrong_data,
        ]);
    }

    # ----------- Helper functions
    private function generate_data_for_table($results, $nums_perlevel) {
        $res_total = $this->calculate_statistic($results, array_sum($nums_perlevel));
        $res_total["level"] = "All";
        // Unique levels
        $levels = array_unique(array_map(function($v) {return $v["level"];}, $results));
        $res_perlevel = [];
        foreach ($levels as $level) {
            $results_sub = array_filter($results, function($v) use ($level) {
                return $v["level"] == $level;
            });
            $res = $this->calculate_statistic($results_sub, $nums_perlevel[$level]);
            $res["level"] = $level;
            array_push($res_perlevel, $res);
        }
        return [$res_total, $res_perlevel];
    }

    private function calculate_statistic($a, $total_count) {
        $ret = ["count" => 0, "correct" => 0, "correct_rate" => 0, "time_mean" => 0,
            "time_var" => 0, "time_stat" => 0];
        foreach ($a as $key => $row) {
            $ret["count"]++;
            $ret["correct"] += $row["is_correct"];
            $ret["time_mean"] += $row["elapsed_time"];
        }
        if ($ret["count"] == 0) {
            return $ret;
        }
        $ret["time_mean"] /= $ret["count"];
        foreach ($a as $key => $row) {
            $ret["time_var"] += ($row["elapsed_time"] - $ret["time_mean"]) *
                ($row["elapsed_time"] - $ret["time_mean"]);
        }
        $ret["time_var"] = sqrt($ret["time_var"] / $ret["count"]);
        $ret["time_stat"] = $ret["time_var"] / $ret["time_mean"];
        $ret["correct_rate"] = $ret["correct"] / $total_count * 100;
        $ret["incorrect_rate"] = ($ret["count"] - $ret["correct"])  / $total_count * 100;
        return $ret;
    }
}
