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

            </div>
        </div>
    </table>
@stop