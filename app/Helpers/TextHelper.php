<?php

namespace App\Helpers;

class TextHelper {
    public static function lst_ans_header($index1, $index2) {
        return ($index1 + 1).chr(ord("A") + $index2);
    }
    public static function lst_ans_header_total($index) {
        return ($index + 1)."";
    }
    public static function lst_ans_col_span($answer) {
        $ret = 0;
        foreach ($answer["num_sets"] as $num_set) {
            foreach ($num_set["questions"] as $question) {
                $ret += 5;
            }
            $ret += 1;
        }
        $ret += 1;
        return $ret;
    }
}