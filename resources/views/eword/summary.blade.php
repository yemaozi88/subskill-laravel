@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>回答結果</h1>
</div>

<div class="row">
    <div class="col-md-12">
    @php
    $res_list = [$total_data, $correct_data, $wrong_data];
    $title_list = ["全体結果", "正答した問題の結果", "誤答した問題の結果"];
    $right_num_header_list = ["正答数", "正答数[問]", "誤答数[問]"];
    @endphp
    @for ($i = 0; $i < 3; $i++)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $title_list[$i] }}</h3>
            </div>
            <div class="panel-body">
                @if ($i==0)
                <table class="table table-hover">
                    @include('eword._summary_table_title', [
                        'right_num_header' => $right_num_header_list[$i],
                        'is_show_incorrect_rate' => $i == 2,
                    ])
                    @include('eword._summary_table_row', [
                        'data' => $res_list[$i][0],
                        'no_total' => $i != 0,
                        'reverse_correct_num' => $i == 2,
                    ])
                </table>
                @endif
                <table class="table table-hover">
                    @include('eword._summary_table_title', [
                        'right_num_header' => $right_num_header_list[$i],
                        'is_show_incorrect_rate' => $i == 2,
                    ])
                    @foreach ($res_list[$i][1] as $res)
                        @include('eword._summary_table_row', [
                            'data' => $res,
                            'no_total' => $i != 0,
                            'reverse_correct_num' => $i == 2,
                        ])
                    @endforeach
                </table>
            </div>
        </div>
    @endfor
        <a class="btn btn-default" href="{{ url('/') }}">ホームページに戻る</a>
    </div>
</div>

@stop