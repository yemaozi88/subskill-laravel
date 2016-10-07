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
}