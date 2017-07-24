@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ $title }}--統計</h1>
    </div>
    @if (count($dates) > 0)
    <table class="table table-hover">
        <tr>
            <th>日付</th>
            <th>受験者数</th>
            <th>操作</th>
        </tr>
        @foreach ($dates as $date)
        <tr>
            <td>{{ $date->created_date }}</td>
            <td>{{ $date->count }}</td>
            <td>
                <a class="btn btn-default"
                   href="{{ URL::action($action_name, [
                        'group_name' => $group_name,
                        'date' => $date->created_date,
                   ]) }}"><span class="glyphicon glyphicon-th-list"></span> 詳細</a>
                <a class="btn btn-default" href="{{
                    URL::action($action_name, [
                        'group_name' => $group_name,
                        'date' => $date->created_date,
                        'format' => 'csv',
                   ])
                }}">
                    <span class="glyphicon glyphicon-download"></span> ダウンロード
                </a>
            </td>
        </tr>
        @endforeach
    </table>
    @else
    <p>結果なし</p>
    @endif
@stop