@extends('layouts.app')

@if (false)<html xmlns:v-on="http://www.w3.org/1999/xhtml" xmlns:v-bind="http://www.w3.org/1999/xhtml"> @endif

@section('title', '作動記憶クイズ')

@push('scripts')
    <script src="{{ URL::asset("js/vue.js") }}"></script>
@endpush

@section('content')

    <div id="app">
        <div class="row" v-if="showIntro">
            <div class="col-md-12">
                <div class="page-header">
                    <h1>回答方法の説明</h1>
                </div>
                <p>
                    こんにちは{{ $username }}さん。
                </p>
                <p>
                    これは英単語を聴いて、その意味を答えるクイズです。
                </p>
                <p>
                    これはあなたが一度にどれだけの英文を処理できるかを測定するテストです。<br>
                    「問題を再生」ボタンを押すと、2 文の英文が連続して流れます。<br>
                    英文は一度しか流れず、聞きなおすことはできません。
                </p>
                <p>
                    再生が終了すると、1.5秒後に解答欄が表示されます。<br>
                    それぞれの文章の最後の単語、および内容の正誤を答えてください。<br>
                </p>
                <p>
                    問題は全部で<strong>{{ $q_num }}</strong>問です。
                </p>
                <button class="btn btn-primary"
                        v-on:click="introBtnClicked">
                    始める</button>
            </div>
        </div>
        <div v-if="showQuiz">
            <quiz-index-header :index="quizIndex"></quiz-index-header>
            <quiz-player :audio-wav-srcs="audioWavSrcs" only-once v-on:play-finished="audioPlayFinished"></quiz-player>
            <div v-if="showQuestion">
                <wm-question v-for="(q, index) in quizContents" :index="index + 1" :word-prefix="q.firstChar"></wm-question>
                <button class="btn btn-primary" v-on:click="submit">回答を送信</button>
            </div>
        </div>
    </div>

    <script src="{{ URL::asset("js/lst/quiz.js") }}"></script>

@stop
