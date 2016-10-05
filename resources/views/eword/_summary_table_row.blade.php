<!-- $data, $no_total, $reverse_correct_num -->

<tr>
    <td>{{ $data["level"] }}</td>
    @if ($no_total)
    <td>{{ $reverse_correct_num ? $data["count"] - $data["correct"] : $data["correct"] }}</td>
    @else
    <td>{{ $data["count"] }}問中{{ $data["correct"] }}問</td>
    @endif

    <td>{{ number_format($reverse_correct_num ? $data["incorrect_rate"] : $data["correct_rate"], 2) }}</td>
    <td>{{ number_format($data["time_mean"], 2) }}</td>
    <td>{{ number_format($data["time_var"], 2) }}</td>
    <td>{{ number_format($data["time_stat"], 2) }}</td>
</tr>