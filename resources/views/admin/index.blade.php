@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1>管理員ページ</h1>
    </div>

    <h3 class="">英単語クイズ</h3>
    <ul>
        <li>
            <a href="{{ URL::action('AdminController@eword') }}">成績の閲覧</a>
        </li>
        <li>
            <a href="#">問題管理</a>
        </li>
    </ul>

    <h3 class="">作動記憶クイズ</h3>
    <ul>
        <li>
            <a href="#">成績の閲覧</a>
        </li>
        <li>
            <a href="#">問題管理</a>
        </li>
    </ul>
@stop