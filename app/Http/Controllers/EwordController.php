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

    public function result() {

    }

    public function summary() {

    }
}
