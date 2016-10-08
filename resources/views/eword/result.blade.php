@extends('layouts.app')

@section('scripts')
    @parent
    <script src="{{ URL::asset('js/eword/result.js') }}"></script>
@stop

@section('content')

@if ($is_redirect)
<div id="redirect"></div>
@endif

<div class="page-header">
    <h1>第{{ $q_index }}問</h1>
</div>

<div class="row">
    <div class="col-md-12">
        @if (!$is_redirect)
            @if ($is_blank_answer)
                時間切れです。20秒内に回答してください。<br>
            @elseif ($is_duplicated_post)
                答えを重複に送信しないでくだいさい。<br>
            @elseif (!$is_test)
                あなたの答えは<strong>{{ $is_correct ? "正解" : "不正解" }}</strong>でした。<br>
            @endif
            @if (!$is_test)
                提示された単語は<strong>{{ $q_data[0] }}</strong>、
                意味は<strong>{{ $q_data[$q_data[5]] }}</strong>です。<br>
                @if (!$is_blank_answer)
                回答時間：<strong>{{ number_format($elapsed_time, 2) }}</strong>秒。
                @endif
            @endif
        @endif
        <form class="" id="question_form"
              action="{{ $is_last ? URL::action('EwordController@summary') : URL::action('EwordController@quiz') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="username" value="{{ $username }}">
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <input type="hidden" name="q_set" value="{{ $q_set }}">
            <input type="hidden" name="session_id" value="{{ $session_id }}">
            <input type="hidden" name="with_wav" value="{{ $with_wav ? 1 : 0 }}">
            <input type="hidden" name="is_test" value="{{ $is_test ? 1 : 0 }}">
            @if (!$is_redirect)
            <button type="submit" class="btn btn-default">{{ $is_last ? "結果一覧>>" : "次の問題>>" }} </button>
            @endif
        </form>
    </div>

</div>

@stop