@extends('layouts.app')

@section('content')

<script src="{{ URL::asset('js/eword_index.js') }}"></script>

<div class="page-header">
    <h1>{{ $with_wav ? "聴いて答える問題" : "見て答える問題" }}
        （{{ $is_test ? "実力テスト" : "練習" }}）
    <small>情報登録</small>
    </h1>
</div>

<form class="" action="{{ URL::action('EwordController@intro') }}" method="post">
    <div class="form-group">
        <label for="username">Username</label>
        <p class="form-control-static">
            お名前（半角英数字のみ、スペースは含めない）を入力してください。
        </p>
        <input type="text" class="form-control" id="username"
               name="username" placeholder="Username">
    </div>
    @if (!$is_test)
        <div class="form-group">
            <label for="q_set">Question Set</label>
            <p class="form-control-static">
                どの問題セットに挑戦しますか？
            </p>
            <!-- TODO: replace options with real list -->
            <select class="form-control" name="q_set" id="q_set">
                <option value="A">練習A</option>
                <option value="B">練習B</option>
                <option value="1">レベル1</option>
                <option value="2">レベル2</option>
                <option value="3">レベル3</option>
                <option value="4">レベル4</option>
                <option value="5">レベル5</option>
                <option value="6">レベル6</option>
                <option value="7">レベル7</option>
            </select>
        </div>
        <input type="hidden" name="group_name" value="practice">
    @else
        <div class="form-group">
            <label for="q_set">Select Your College</label>
            <p class="form-control-static">
                学校名または所属先を選択してください。
            </p>

            <div class="container"><div class="row">
                @foreach ($college_info as $index => $info)
                    <div class="col col-md-6">
                        <div class="radio">
                            <label>
                                <input type="radio" name="group_name" {{ $index == 0 ? "checked" : "" }}
                                value="{{ $info[0] }}">
                                @if ($info[0] == 'guest')
                                <p>その他</p>
                                @else
                                <img class="img-responsive col-logo" src="{{ URL::asset('img') . '/' .  $info[1] }}">
                                @endif
                            </label>
                        </div>
                    </div>
                @endforeach
            </div></div>
        </div>
        <input type="hidden" name="q_set" value="R">
    @endif
    {{ csrf_field() }}
    <input type="hidden" name="with_wav" value="{{ $with_wav ? 1 : 0 }} ?>">
    <input type="hidden" name="is_test" value="{{ $is_test ? 1 : 0 }}">
    <button type="submit" class="btn btn-default">次に進む</button>
</form>

@stop
