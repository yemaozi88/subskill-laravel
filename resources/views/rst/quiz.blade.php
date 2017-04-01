@extends('layouts.app')

@if (false)<html xmlns:v-on="http://www.w3.org/1999/xhtml" xmlns:v-bind="http://www.w3.org/1999/xhtml"> @endif

@section('title', '作動記憶クイズ(Reading)')

@section('content')

    <div id="app">
        <rst-app username="{{ $username }}"
                 group-name="{{ $group_name }}"
                 manifest-url="{{ $manifest_url }}"
                 send-answer-url="{{ $send_answer_url }}"
                 :show-answer="{{ $show_answer }}"
                 quiz-set-name="{{ $quiz_set_name }}"
                 :question-num="{{ $q_num }}"></rst-app>
    </div>

    <script src="{{ URL::asset("js/rst/quiz.js") }}"></script>

@stop
