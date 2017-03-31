@extends('layouts.app')

@if (false)<html xmlns:v-on="http://www.w3.org/1999/xhtml" xmlns:v-bind="http://www.w3.org/1999/xhtml"> @endif

@section('title', '作動記憶クイズ(Listening)')

@section('content')

    <div id="config" data-manifest-url="{{ $manifest_url }}" data-audio-folder-url="{{ $audio_folder_url }}"
         data-send-answer-url="{{ $send_answer_url }}" data-username="{{ $username }}"
         data-group-name="{{ $group_name }}" data-quiz-set-name="{{ $quiz_set_name }}"
         data-show-answer="{{ $show_answer }}"></div>
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
                    これはあなたが一度にどれだけの英文を処理できるかを測定するテストです。<br>
                    「問題を再生」ボタンを押すと、数文の英文が連続して流れます。<br>
                    英文は一度しか流れず、聞きなおすことはできません。
                </p>
                <p>
                    再生が終了すると、解答欄が表示されます。<br>
                    それぞれの文章の最後の単語、および内容の正誤を答えてください。<br>
                </p>
                <p>
                    問題は全部で<strong>{{ $q_num }}</strong>問です。
                </p>
                <button class="btn btn-primary"
                        v-on:click="introBtnClicked"
                        :disabled="!isDataLoaded">
                    始める</button>
            </div>
        </div>
        <div class="row" v-if="showFinishedMessage">
            <div class="col-md-12">
                <p>全ての回答を送信しました。</p>
                <a href="{{ url('/') }}" class="btn btn-default">ホームページに戻る</a>
            </div>
        </div>
        <div class="row" v-if="showWaiting">
            <div class="col-md-12">
                <p>データ送信中...</p>
                <div class="loader"></div>
            </div>
        </div>
        <div v-if="showQuiz">
            <quiz-index-header :index="setIndex + 1" :question-num="questionNum"></quiz-index-header>
            <quiz-player v-for="(wavs, index) in allWavs"
                         :audio-wav-srcs="wavs"
                         only-once
                         v-if="index == setIndex"
                         v-on:play-finished="audioPlayFinished"></quiz-player>
            <div v-if="showQuestion">
                <wm-question v-for="(q, index) in quizContents" :index="index + 1" :word-prefix="q.firstChar" ref="questions">
                </wm-question>
                <button class="btn btn-primary" v-on:click="submit">@{{ submitBtnText }}</button>
            </div>
        </div>
        <div class="row" v-if="showAnswer">
            <div class="col-md-12">
                <div v-for="(q, index) in quizContents">
                    <h3 class="subtitle">第@{{ index + 1 }}文</h3>
                    <p>
                        正解：@{{ rawData.questionSets[setIndex].answers[index].correctness ? "正しい" : "誤り" }},
                        @{{ rawData.questionSets[setIndex].answers[index].lastWord }}
                    </p>
                    <p>
                        あなたの回答：@{{ $refs.questions[index].answer1 == 1 ? "正しい" : "誤り" }},
                        @{{ $refs.questions[index].wordPrefix + $refs.questions[index].answer2 }}
                    </p>
                </div>
                <button class="btn btn-primary" v-on:click="submit">@{{ submitBtnText }}</button>
            </div>
        </div>
    </div>

    <script src="{{ URL::asset("js/lst/quiz.js") }}"></script>

@stop
