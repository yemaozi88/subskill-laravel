/**
 * Created by sonicmisora on 2017/3/31.
 */

var module = {};

/**
 * Check if a char is one of the following:
 * a-z, A-Z, _, 0-9
 * If it is, return true. Otherwise return false.
 * @param char {String}
 */
module.isNamableChar = function (char) {
    var charCode = char.charCodeAt(0);
    if (charCode >= 'a'.charCodeAt(0) && charCode <= 'z'.charCodeAt(0) ||
        charCode >= 'A'.charCodeAt(0) && charCode <= 'Z'.charCodeAt(0) ||
        charCode >= '0'.charCodeAt(0) && charCode <= '9'.charCodeAt(0) ||
        char == '_') {
        return true;
    }
    return false;
};

/**
 * Validate if the username is legal.
 * An array containing true or false will be returned.
 * If it is false, the second element would be the reason.
 * @param username {String}
 * @return {Array}
 */
module.validateUsername = function (username) {
    if (username.length < 4 || username.length > 50) {
        return [false, "Usernameの文字数は4以上かつ50以下でなければならない。"];
    }
    for (var i = 0; i < username.length; i++) {
        if (!module.isNamableChar(username[i])) {
            return [false, "Usernameに使用可能なのは英文字と数字のみ。"];
        }
    }
    return [true];
};

module.isEnglishChar = function (char) {
    var charCode = char.charCodeAt(0);
    if (charCode >= 'a'.charCodeAt(0) && charCode <= 'z'.charCodeAt(0) ||
        charCode >= 'A'.charCodeAt(0) && charCode <= 'Z'.charCodeAt(0) ||
        char == '-') {
        return true;
    }
    return false;
};

/**
 * Validate if the word is legal English word.
 * A legal English word is defined as:
 * Contains only letters or "-".
 */
module.validateEnglishWord = function (username) {
    for (var i = 0; i < username.length; i++) {
        if (!module.isEnglishChar(username[i])) {
            return false;
        }
    }
    return true;
};

/**
 * Get a unique string generated randomly.
 */
module.getUniqueStr = function (myStrong) {
    var strong = 1000;
    if (myStrong) strong = myStrong;
    return new Date().getTime().toString(16)  + Math.floor(strong*Math.random()).toString(16)
};

window.Helpers = module;