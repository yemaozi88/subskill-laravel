@extends('layouts.app')

@section('scripts')
    @parent
    <script src="{{ URL::asset('js/eword/quiz.js') }}"></script>
@stop

@section('content')

<div class="page-header">
    <h1>第{{ $q_index }}問</h1>
</div>

<div class="row">
    <div class="col-md-6">
        @if ($with_wav)
        <button class="btn btn-default" type="button" id="play_audio" disabled>再生</button>
        <audio id="question_audio" preload="auto">
            @if (isset($wav_path))
            <source src="{{ $wav_path }}" type="audio/wav" />
            @endif
            @if (isset($mp3_path))
            <source src="{{ $mp3_path }}" type="audio/mp3" />
            @endif
            Your browser does not support the audio element.
        </audio>
        @else
        <strong>{{ $q_data[0] }}</strong>
        @endif

        <form class="" id="question_form" action="{{ URL::action('EwordController@result') }}" method="post">
            <div class="form-group">
                @for ($i = 0; $i < 4; $i++)
                <div class="radio">
                    <label>
                        <input type="radio" name="q_selection" id="q_selection_{{ $i + 1 }}"
                               value="{{ $i + 1 }}" disabled>
                        {{ $q_data[$i + 1] }}
                    </label>
                </div>
                @endfor
                    <input type="radio" name="q_selection" id="q_selection_blank"
                           value="0" hidden>
            </div>
            {{ csrf_field() }}
            <input type="hidden" name="username" value="{{ $username }}">
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <input type="hidden" name="q_set" value="{{ $q_set }}">
            <input type="hidden" name="q_index" value="{{ $q_index }}">
            <input type="hidden" name="session_id" value="{{ $session_id }}">
            <input type="hidden" name="with_wav" value="{{ $with_wav ? 1 : 0 }}">
            <input type="hidden" name="is_test" value="{{ $is_test ? 1 : 0 }}">
            <input type="hidden" name="play_count" id="play_count" value="0">
            <input type="hidden" name="elapsed_time" id="elapsed_time" value="0">
        </form>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="well well-sm timer">
                    <h2>
                        残り時間:<strong><span>20.0</span></strong>秒
                    </h2>
                </div>
                @if (!$is_test)
                <h5>[これまでの成績]</h5>
                <p>{{ $q_index - 1 }}問中<strong>{{ $correct_num }}</strong>問正解
                    (正解率{{ $q_index > 1 ? number_format($correct_num / ($q_index - 1) * 100, 2) : 0 }}%)</p>
                <p>
                @endif
                @if ($q_index < $q_num)
                    あと残り{{ $q_num - $q_index }}問です。
                @else
                    これが最後の問題です。
                @endif
                </p>
            </div>
        </div>
    </div>

</div>

@stop
