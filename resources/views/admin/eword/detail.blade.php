@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ $date }}の統計</h1>
    </div>

    <table class="table table-hover">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">全体</h3>
            </div>
            <div class="panel-body">
                <h4 class="subtitle">正答率[%]</h4>
                @if (count($overall_stat['correct_rate']) > 0)
                <table class="table table-hover">
                    <tr>
                        <th>氏名</th>
                        @foreach(reset($overall_stat['correct_rate']) as $key => $value)
                        <th>{{ $key == 't' ? '全体' : "レベル$key" }}</th>
                        @endforeach
                    </tr>
                    @foreach($overall_stat['correct_rate'] as $username => $entry)
                        <tr>
                            <td>{{ $username }}</td>
                            @foreach($entry as $key => $value)
                                <td>{{ $value * 100.0 }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
                @else
                <p>ガラ空き</p>
                @endif
            </div>
        </div>
    </table>

    <div>
      
        @foreach ($results as $result)
            {{ $result }}<br>
        @endforeach
    </div>
@stop