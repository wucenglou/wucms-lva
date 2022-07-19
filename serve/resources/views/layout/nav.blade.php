<div class="blog-masthead">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#example-navbar-collapse">
                <span class="sr-only">切换导航</span>
                <a href="#">
                    <span class="glyphicon glyphicon-th-large"></span>
                </a>
            </button>
            <a class="navbar-brand" href="/posts">环宇之下</a>
        </div>
        <div class="collapse navbar-collapse" id="example-navbar-collapse">
            <form action="/posts/search" method="GET">
                <ul class="nav navbar-nav navbar-left">
                    <li>
                        <a class="blog-nav-item " href="/posts">首页</a>
                    </li>
                    <li>
                        <a class="blog-nav-item" href="/posts/create">写文章</a>
                    </li>
                    <li>
                        <a class="blog-nav-item" href="/notices">通知</a>
                    </li>
                    <li>
                        <input name="keyword" type="text" value="{{ Request::input('keyword') }}"
                            class="form-control" style="margin-top:10px" placeholder="搜索词">
                    </li>
                    <li>
                        <button class="btn btn-primary" style="margin-top:10px" type="submit">搜索</button>
                    </li>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        {{-- <div> --}}
                            @if (Auth::check())
                                {{-- <img src="{{Auth::user()->avatar()}}" alt="" class="img-rounded" style="border-radius:500px; height: 30px;"> --}}
                                <a href="#" class="blog-nav-item dropdown-toggle" data-toggle="dropdown" role="button"
                                    aria-haspopup="true" aria-expanded="false">
                                    {{ Auth::user()->username }}
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="/user/{{ \Auth::id() }}">我的主页</a></li>
                                    <li><a href="/user/me/setting">个人设置</a></li>
                                    <li><a href="/logout">登出</a></li>
                                </ul>
                            @else
                                <div class="col-md-10" style="background-color: #dedef8;">
                                    <a href="/login">登录</a>
                                </div>
                            @endif

                        {{-- </div> --}}
                    </li>
                </ul>
            </form>
        </div>
    </div>
</div>
