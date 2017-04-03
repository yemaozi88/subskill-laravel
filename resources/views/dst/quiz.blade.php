@extends('layouts.app')

@if (false)<html xmlns:v-on="http://www.w3.org/1999/xhtml" xmlns:v-bind="http://www.w3.org/1999/xhtml"> @endif

@section('title', '作動記憶クイズ(数字)')

@section('content')

    <div id="app">
        <dst-app username="{{ $username }}"
                 group-name="{{ $group_name }}"
                 manifest-url="{{ $manifest_url }}"
                 send-answer-url="{{ $send_answer_url }}"
                 :show-answer="{{ $show_answer }}"
                 quiz-set-name="{{ $quiz_set_name }}"
                 :question-num="{{ $q_num }}"></dst-app>
    </div>

    <script src="{{ URL::asset("js/dst/quiz.js") }}"></script>

@stop
