@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>回答方法の説明</h1>
</div>

<div class="row">
    <div class="col-md-12">
        <p>
            こんにちは{{ $username }}さん。
        </p>
        @if (!$with_wav)
        <p>
            これは英単語を見て、その意味を答えるクイズです。
        </p>
        <p>
            問題画面に表示されている単語の和訳として、もっとも適当なものを選択肢の中から選んでください。<br>
            回答は一度選択されるとすぐに送信され、後から変更することはできません。<br>
            またタイマーは問題が表示されると同時に動き出し、回答が送信されるまで止まりません。
        </p>
        @else
        <p>
            これは英単語を聴いて、その意味を答えるクイズです。
        </p>
        <p>
            Playボタンをクリックすると、タイマーが動き出し、問題の音声が流れます。<br>
            （ブラウザが音声を読み込むまでに数秒かかる場合もあります）<br>
            その音声の和訳として、もっとも適当なものを選択肢の中から選んでください。
        </p>
        <p>
            音声は何度聞きなおしてもかまいません。<br>
            音声を聞いている途中で回答してもかまいません。<br>
            ただし回答は一度選択されるとすぐに送信され、後から変更することはできません。<br>
            また一度動き出したタイマーは回答が送信されるまで止まりません。
        </p>
        @endif
        <p>
            問題は全部で<strong>{{ $q_num }}</strong>問です。
        </p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <form class="" action="{{ URL::action('EwordController@quiz') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="username" value="{{ $username }}">
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <input type="hidden" name="q_set" value="{{ $q_set }}">
            <input type="hidden" name="session_id" value="{{ $session_id }}">
            <input type="hidden" name="with_wav" value="{{ $with_wav ? 1 : 0 }}">
            <input type="hidden" name="is_test" value="{{ $is_test ? 1 : 0 }}">
            <button type="submit" class="btn btn-primary">始める</button>
        </form>
    </div>
</div>

@stop