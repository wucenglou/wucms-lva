<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="description" content="">
    <title>登陆</title>
    <link href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://v3.bootcss.com/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <link href="http://v3.bootcss.com/examples/signin/signin.css" rel="stylesheet">
</head>

<body>

    <div class="container">

        <form class="form-signin" method="POST" action="/login">
            {{csrf_field()}}
            <h2 class="form-signin-heading">请登录</h2>
            {{-- <label for="inputEmail" class="sr-only">用户名</label> --}}
            <input type="txt" name="username" id="inputEmail" class="form-control" placeholder="用户名" required autofocus>
            <label for="inputPassword" class="sr-only">密码</label>
            <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="1" name="is_remember"> 记住我
                </label>
            </div>
            @include('layout.error')
            <button class="btn btn-lg btn-primary btn-block" type="submit">登陆</button>
            <a href="/register" class="btn btn-lg btn-primary btn-block" type="submit">去注册>></a>
        </form>

    </div> <!-- /container -->

</body>

</html>