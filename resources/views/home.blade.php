@extends('layouts.app')

@section('content')

<div class="jumbotron">
    <h1>速読と速聴のための英単語力クイズへようこそ！</h1>
    <a href="{{ URL::action('EwordController@index') }}" class="btn btn-primary">練習</a>
</div>

@stop