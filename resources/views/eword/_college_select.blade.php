<!-- $college_info -->
<div class="form-group">
    <label for="q_set">Select Your College</label>
    <p class="form-control-static">
        学校名または所属先を選択してください。
    </p>

    <div class="container">
        <div class="row">
            @foreach ($college_info as $index => $info)
                <div class="col col-md-6">
                    <div class="radio">
                        <label>
                            <input type="radio" name="group_name" {{ $index == 0 ? "checked" : "" }}
                            value="{{ $info[0] }}">
                            @if ($info[0] == 'guest')
                                <p>その他</p>
                            @else
                                <img class="img-responsive col-logo" src="{{ URL::asset('img') . '/' .  $info[1] }}">
                            @endif
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>