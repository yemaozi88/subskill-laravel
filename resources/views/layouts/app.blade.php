<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="{{ URL::asset("js/jquery.min.js") }}"></script>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset("css/bootstrap.min.css") }}">

    <!-- Optional theme -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset("css/bootstrap-theme.min.css") }}">

    <!-- Latest compiled and minified JavaScript -->
    <script src="{{ URL::asset("js/bootstrap.min.js") }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ URL::asset("css/framework.css") }}">

    <link rel="stylesheet" type="text/css" href="{{ URL::asset("css/custom.css") }}">

    <title>
        @yield('title', '速読と速聴のための英単語力クイズ')
    </title>
</head>
<body>
<header>
    @include('layouts.navigation')
</header>
<main role="main">
    <div class="container">
        @section('content')
            Default Content.
        @show
    </div>
</main>
<footer>
    @include('layouts.footer')
</footer>
</body>
</html>
