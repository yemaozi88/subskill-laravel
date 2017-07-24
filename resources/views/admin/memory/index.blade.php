@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>記憶クイズ(Listening)の統計</h1>
    </div>
    <form class="" action="{{ $submit_url }}" method="get">
        @include('eword._college_select', ['college_info' => $college_info])
        <button type="submit" class="btn btn-default">調べる</button>
    </form>
@stop