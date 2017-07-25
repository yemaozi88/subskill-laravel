<?php

namespace App\Helpers;

class Helper {

    /**
     * @return array
     */
    public static function get_college_info() {
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
        return $college_info;
    }
    /*
     * Return value is like:
     * [
     *  // user1
     *  [
     *    username: "username",
     *    answers: [
     *      [
     *        q_num: 2,
     *        avg_score: 1,
     *        num_sets: [
     *          sum_score: 2,
     *          questions: [
     *            [
     *              c_score: 1,
     *              w_score: 1,
     *              score: 1,
     *              right_word: "abc",
     *              input_word: "abc"
     *            ],
     *            ...
     *          ],
     *          ...
     *        ]
     *      ],
     *      ...
     *    ]
     *  ],
     *  // user2
     *  [],
     *  ...
     * ]
     */
    public static function processResult($question_sets, $results) {
        $set_question_num = array_reduce($question_sets,
            function ($c, $x) { return $c + $x->questionNum; }, 0);
        $grouped_question_sets = [];
        // Group these questions by their questionNum to fit the required output
        foreach ($question_sets as $question) {
            if (!array_key_exists($question->questionNum, $grouped_question_sets)) {
                $grouped_question_sets[$question->questionNum] = [];
            }
            array_push($grouped_question_sets[$question->questionNum], $question);
        }
        $ret = [];
        foreach ($results as $result) {
            $username = $result->user->username;
            $question_num = $result->question_num;
            $judgement_list = $result->judgement_list;
            $last_word_list =$result->last_word_list;
            if ($question_num != $set_question_num) {
                // There are some test data so these two numbers are not the same.
                // Just simply skip these test data.
                continue;
            }
            // The first element in these two arrays should be empty string
            // some thing like: "", "T", "F", "T", ...
            $j_array = explode("#", $judgement_list);
            // some thing like: "", "dog", "cat", ...
            $w_array = explode("#", $last_word_list);
            $index = 0;
            $user = ["username" => $username, "max_score" => 0, "answers" => []];
            foreach ($grouped_question_sets as $q_num => $grouped_questions) {
                $t_answer = ["avg_score" => 0, "q_num" => $q_num, "num_sets" => []];
                $t_sum = 0;
                foreach ($grouped_questions as $question) {
                    $num_set = ["questions" => [], "sum_score" => 0];
                    $sum_score = 0;
                    foreach ($question->answers as $answer) {
                        $index++;
                        $t = [];
                        if (($j_array[$index] == "T") == $answer->correctness) {
                            $t["c_score"] = 1;
                        } else {
                            $t["c_score"] = 0;
                        }
                        if ($w_array[$index] == $answer->lastWord) {
                            $t["w_score"] = 1;
                        } else {
                            $t["w_score"] = 0;
                        }
                        $t["right_word"] = $answer->lastWord;
                        $t["input_word"] = $w_array[$index];
                        $t["score"] = $t["c_score"] * $t["w_score"];
                        $sum_score += $t["score"];
                        array_push($num_set["questions"], $t);
                    }
                    $num_set["sum_score"] = $sum_score;
                    $t_sum += $sum_score;
                    array_push($t_answer["num_sets"], $num_set);
                }
                $t_answer["avg_score"] = $t_sum / count($t_answer["num_sets"]);
                array_push($user["answers"], $t_answer);
            }
            $user["max_score"] = array_reduce($user["answers"],
                function($c, $x) { return max($c, $x["avg_score"]); }, 0);
            array_push($ret, $user);
        }
        return $ret;
    }

    public static function toCsv($results) {
        $ret = '';
        foreach ($results as $user) {
            $ret .= $user["username"].",,";
            foreach ($user["answers"] as $answer) {
                foreach ($answer["num_sets"] as $num_set) {
                    foreach ($num_set["questions"] as $question) {
                        $ret .= $question["c_score"].",";
                        $ret .= $question["input_word"].",";
                        $ret .= $question["right_word"].",";
                        $ret .= $question["w_score"].",";
                        $ret .= $question["score"].",";
                    }
                    $ret .= $num_set["sum_score"].",";
                }
                $ret .= $answer["avg_score"].",";
            }
            $ret .= $user["max_score"]."\n";
        }
        return $ret;
    }
}