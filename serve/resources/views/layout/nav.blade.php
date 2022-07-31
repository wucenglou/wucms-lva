{{-- <div class=""> --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">企鹅社区</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/posts">首页</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="/posts/create" class="nav-link" href="/posts/create">写文章</a>
                    </li>
                    <li class="nav-item">
                        <a href="/notices" class="nav-link" href="/notices">通知</a>
                    </li> --}}
                    @foreach ($navs as $nav)
                        
                        @if (!empty($nav['children']))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="/cn/{{ $nav['name'] }}/{{ $nav['id'] }}" id="navbardrop"
                                data-bs-toggle="dropdown">
                                {{ $nav['metaTitle'] }}
                            </a>
                            <div class="dropdown-menu">
                                @foreach ($nav['children'] as $n)
                                <a class="dropdown-item" href="/cn/{{ $n['name'] }}/{{$n['id'] }}">{{ $n['metaTitle'] }}</a>
                                @endforeach
                            </div>
                        </li>
                        @else
                            <li class="nav-item"><a class="nav-link"
                                    href="/cn/{{ $nav['name'] }}/{{$nav['id']}}">{{ $nav['metaTitle'] }}</a>
                        @endif
                        </li>
                    @endforeach
                    <form class="d-flex" role="search" action="/posts/search" method="GET" style="margin-left: 1rem">
                        <input class="form-control me-2" type="search" name="keyword"
                            value="{{ Request::input('keyword') }}" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </ul>
                <span class="navbar-text">
                    <div class="dropdown">
                        @if (Auth::check())
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                {{ Auth::user()->username }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/user/{{ \Auth::id() }}">我的主页</a></li>
                                <li><a class="dropdown-item" href="/user/me/setting">个人设置</a></li>
                                <li><a class="dropdown-item" href="/logout">登出</a></li>
                            </ul>
                        @else
                            <a href="/login"><button type="button"
                                    class="btn btn-outline-primary me-2">登录</button></a>
                            <a href="/register"><button type="button" class="btn btn-outline-primary">注册</button></a>
                        @endif
                    </div>
                </span>
            </div>
        </div>
    </nav>
{{-- </div> --}}
