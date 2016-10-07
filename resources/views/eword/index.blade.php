@extends('layouts.app')

@section('scripts')
    @parent
    <script src="{{ URL::asset('js/eword/index.js') }}"></script>
@stop

@section('content')

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
        @include('eword._college_select', ['college_info' => $college_info])
        <input type="hidden" name="q_set" value="R">
    @endif
    {{ csrf_field() }}
    <input type="hidden" name="with_wav" value="{{ $with_wav ? 1 : 0 }} ?>">
    <input type="hidden" name="is_test" value="{{ $is_test ? 1 : 0 }}">
    <button type="submit" class="btn btn-default">次に進む</button>
</form>

@stop
