@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ $test_type == 'with_wav' ? '聴いて答える' : '見て答える' }}--統計</h1>
    </div>
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
                       href="{{ URL::action('Admin\EwordController@detail', [
                            'test_type' => $test_type,
                            'group_name' => $group_name,
                            'date' => $date->created_date,
                       ]) }}">詳細</a>
                </td>
            </tr>
        @endforeach
    </table>
@stop