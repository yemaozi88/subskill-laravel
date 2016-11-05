<!-- $stat -->
@if (count($stat) > 0)
    <table class="table table-hover">
        <tr>
            <th>氏名</th>
            @foreach(reset($stat) as $key => $value)
                <th>{{ $key == 't' ? '全体' : "レベル$key" }}</th>
            @endforeach
        </tr>
        @foreach($stat as $username => $entry)
            <tr>
                <td>{{ $username }}</td>
                @foreach($entry as $key => $value)
                    <td>{{ number_format($value, 2) }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
@else
    <p>ガラ空き</p>
@endif