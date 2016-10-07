@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>英単語クイズの統計</h1>
    </div>
    <form class="" action="{{ URL::action('AdminController@eword_list') }}" method="get">
        @include('eword._college_select', ['college_info' => $college_info])
        <div class="form-group">
            <label for="test_type">Select Type</label>
            <div class="radio">
                <label>
                    <input type="radio" name="test_type" value="with_wav" checked>
                    聴いて答える
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="test_type" value="without_wav">
                    見て答える
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-default">調べる</button>
    </form>
@stop