@extends('layouts.app')

@section('title', '作動記憶クイズ(数字)')

@push('scripts')
<script src="{{ URL::asset('js/dst/index.js') }}"></script>
@endpush

@section('content')

    <div class="page-header">
        <h1>数字の作動記憶クイズ（{{ $is_test ? "実力テスト" : "練習" }}）
            <small>情報登録</small>
        </h1>
    </div>

    <form class="" action="{{ url('dst/quiz') }}" method="get">
        <div class="form-group">
            <label for="username">Username</label>
            <p class="form-control-static">
                お名前（半角英数字のみ、スペースは含めない）を入力してください。
            </p>
            <input type="text" class="form-control" id="username"
                   name="username" placeholder="Username">
        </div>
        @if ($is_test)
            @include('eword._college_select', ['college_info' => $college_info])
        @else
            <input type="hidden" name="group_name" value="practice">
        @endif

        <input type="hidden" name="is_test" value="{{ $is_test ? 1 : 0 }}">
        <button type="submit" class="btn btn-default">次に進む</button>
    </form>

@stop
