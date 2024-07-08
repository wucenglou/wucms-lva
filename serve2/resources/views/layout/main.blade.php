<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>环宇之下</title>
    <link href="/css/blog.css" rel="stylesheet">
    <link href="https://cdn.staticfile.org/twitter-bootstrap/5.1.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.staticfile.org/twitter-bootstrap/5.1.1/js/bootstrap.bundle.min.js"></script>
    {{-- <link href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"> --}}
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
</head>

<body>
    @include('layout.nav')
    <div class="container">

        <div class="row" style="margin-top: 5rem">
            @yield('content')
            @include('layout.sidebar')
        </div>
    </div>
    @include('layout.footer')
    <script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
    {{-- <script src="https://cdn.bootcdn.net/ajax/libs/zepto/1.2.0/zepto.js"></script> --}}
    {{-- <script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script> --}}
    <script type="text/javascript" src="/js/ylaravel.js"></script>

</body>

</html>
