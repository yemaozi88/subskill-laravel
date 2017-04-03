@section('navi_links')

<li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        英語彙力クイズ
        <span class="caret"></span></a>
    <ul class="dropdown-menu">
        <li><a href="{{ url('eword?with_wav=1&is_test=0') }}">聞いて答える(練習)</a></li>
        <li><a href="{{ url('eword?with_wav=1&is_test=1') }}">聞いて答える(テスト)</a></li>
        <li role="separator" class="divider"></li>
        <li><a href="{{ url('eword?with_wav=0&is_test=0') }}">見て答える(練習)</a></li>
        <li><a href="{{ url('eword?with_wav=0&is_test=1') }}">見て答える(テスト)</a></li>
    </ul>
</li>
<li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        作動記憶クイズ
        <span class="caret"></span></a>
    <ul class="dropdown-menu">
        <li><a href="{{ url('lst') }}">リスニング(練習)</a></li>
        <li><a href="{{ url('lst?is_test=1') }}">リスニング(テスト)</a></li>
        <li role="separator" class="divider"></li>
        <li><a href="{{ url('rst') }}">リーディング(練習)</a></li>
        <li><a href="{{ url('rst?is_test=1') }}">リーディング(テスト)</a></li>
        <li role="separator" class="divider"></li>
        <li><a href="{{ url('dst') }}">数字(練習)</a></li>
        <li><a href="{{ url('dst?is_test=1') }}">数字(テスト)</a></li>
    </ul>
</li>
<li><a href="{{ url('admin') }}"><span class="glyphicon glyphicon-wrench"></span> 先生はこちらから</a></li>

@show