@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>{{ $test_type == 'with_wav' ? '聴いて答える' : '見て答える' }}--統計</h1>
    </div>

@stop