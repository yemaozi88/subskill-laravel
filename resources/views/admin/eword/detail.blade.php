@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ $date }}の統計({{ $group_title }})</h1>
        <a class="btn btn-default" href="{{
                        URL::action('Admin\EwordController@detail', [
                            'test_type' => $test_type,
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
        <div class="panel-body">
            <h4 class="subtitle">正答率[%]</h4>
            @include('admin.eword._detail_table', ['stat' => $overall_stat['correct_rate']])
            <h4 class="subtitle">反応速度[秒]</h4>
            @include('admin.eword._detail_table', ['stat' => $overall_stat['elapsed_time_mean']])
            <h4 class="subtitle">ばらつき[秒]</h4>
            @include('admin.eword._detail_table', ['stat' => $overall_stat['elapsed_time_var']])
            <h4 class="subtitle">安定性[秒]</h4>
            @include('admin.eword._detail_table', ['stat' => $overall_stat['elapsed_time_stab']])
        </div>
    </div>
@stop