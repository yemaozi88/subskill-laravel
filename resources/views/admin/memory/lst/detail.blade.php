@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ $date }}の統計({{ $group_title }})</h1>
        <a class="btn btn-default" href="{{
                        URL::action('Admin\LstController@detail', [
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
            <table class="table table-hover table-bordered">
                <tr>
                    <th></th>
                    <th></th>
                    @foreach($results[0]["answers"] as $answer_index => $answer)
                        <th colspan="{{ THelper::lst_ans_col_span($answer) }}" class="text-center">
                            {{ $answer["q_num"] }}文の組み合わせ
                        </th>
                    @endforeach
                    <th></th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    @php ($all_index = 0)
                    @foreach($results[0]["answers"] as $answer_index => $answer)
                        @foreach ($answer["num_sets"] as $num_set_index => $num_set)
                            @foreach ($num_set["questions"] as $question_index => $question)
                            <th colspan="5" class="text-center">{{ THelper::lst_ans_header($all_index, $question_index) }}</th>
                            @endforeach
                        <th>{{ THelper::lst_ans_header_total($num_set_index) }}</th>
                        @php ($all_index += 1)
                        @endforeach
                    <th>AVG</th>
                    @endforeach
                    <th></th>
                </tr>
                <tr>
                    <th>氏名</th>
                    <th></th>
                    @foreach($results[0]["answers"] as $answer_index => $answer)
                        @foreach ($answer["num_sets"] as $num_set_index => $num_set)
                            @foreach ($num_set["questions"] as $question_index => $question)
                            <th>正誤の得点</th>
                            <th>入力文字列</th>
                            <th>正答文字列</th>
                            <th>文字列得点</th>
                            <th class="text-danger">総合得点</th>
                            @endforeach
                        <th class="text-success">最終得点</th>
                        @endforeach
                    <th class="text-info">総合点の平均値</th>
                    @endforeach
                    <th class="text-info">平均最大値</th>
                </tr>
                @foreach ($results as $user)
                <tr>
                    <td>{{ $user["username"] }}</td>
                    <td></td>
                    @foreach ($user["answers"] as $answer)
                        @foreach ($answer["num_sets"] as $num_set)
                            @foreach($num_set["questions"] as $question_index => $question)
                            <td>{{ $question["c_score"] }}</td>
                            <td>{{ $question["input_word"] }}</td>
                            <td>{{ $question["right_word"] }}</td>
                            <td>{{ $question["w_score"] }}</td>
                            <td class="text-danger">{{ $question["score"] }}</td>
                            @endforeach
                        <td class="text-success">{{ $num_set["sum_score"] }}</td>
                        @endforeach
                    <td class="text-info">{{ $answer["avg_score"] }}</td>
                    @endforeach
                    <td class="text-info">{{ $user["max_score"] }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @else
        <p>結果なし</p>
        @endif
    </div>
@stop