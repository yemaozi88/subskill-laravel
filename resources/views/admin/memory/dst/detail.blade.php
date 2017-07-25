@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ $date }}の統計(Digit)({{ $group_title }})</h1>
        <a class="btn btn-default" href="{{
                        URL::action('Admin\DstController@detail', [
                            'group_name' => $group_name,
                            'date' => $date,
                            'format' => 'csv',
                       ])
                    }}">
            <span class="glyphicon glyphicon-download"></span> ダウンロード
        </a>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">全体</h3>
        </div>
        @if (count($results) > 0)
        <div class="panel-body">
            <p class="text-info">一行目：問題番号 <br> 二行目：桁数（a:正順、b:逆順）</p>
            <table class="table table-hover table-bordered">
                <tr>
                    <th></th>
                    <th></th>
                    @foreach($results[0]["answers"] as $answer)
                        <th class="text-center" colspan="3"> {{ $answer["question_index"] }} </th>
                    @endforeach
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    @foreach($results[0]["answers"] as $answer)
                        <th class="text-center" colspan="3"> {{ $answer["header"] }} </th>
                    @endforeach
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <th>氏名</th>
                    <th></th>
                    @foreach($results[0]["answers"] as $answer)
                        <th> 入力文字列 </th>
                        <th> 正答文字列 </th>
                        <th> 得点 </th>
                    @endforeach
                    <th>正順の最高点</th>
                    <th>逆順の最高点</th>

                </tr>
                @foreach ($results as $user)
                    <tr>
                        <th>{{ $user["username"] }}</th>
                        <th></th>
                        @foreach($user["answers"] as $answer)
                            <th> {{ $answer["input_list"] }} </th>
                            <th> {{ $answer["right_list"] }} </th>
                            <th> {{ $answer["score"] }} </th>
                        @endforeach
                        <th>{{ $user["forward_best"] }}</th>
                        <th>{{ $user["backward_best"] }}</th>

                    </tr>
                @endforeach
            </table>
        </div>
        @else
        <p>結果なし</p>
        @endif
    </div>
@stop