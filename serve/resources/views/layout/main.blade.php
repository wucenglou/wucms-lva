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
    <link href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/wangEditor.min.css">
</head>

<body>
    @include('layout.nav')
    <div class="container">

        <div class="blog-header">
        </div>

        <div class="row">
            @yield("content")
            @include('layout.sidebar')
        </div>
    </div><!-- /.row -->
    @include('layout.footer')
    <script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/js/wangEditor.min.js"></script>
    <script type="text/javascript" src="/js/ylaravel.js"></script>

</body>

</html>
