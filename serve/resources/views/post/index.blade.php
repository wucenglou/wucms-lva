@extends('layout.main')
@section('content')
    {{-- <div class="col-sm-8 blog-main">
        @include('post.carousel')
        <div style="height: 20px;">
        </div>
        <div>
            @foreach ($posts as $post)
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h2 class="blog-post-title"><a href="/posts/{{ $post->id }}">{{ $post->title }}</a></h2>
                        <p class="blog-post-meta">{{ $post->created_at }}
                            <a href="/user/{{ $post->user->id }}">
                                <button type="button" class="btn btn-default btn-sm">
                                    <span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;{{ $post->user->username }}
                                </button>
                            </a>
                        </p>
                        {!! Str::limit($post->content, 260, '...') !!}
                        <p class="blog-post-meta">点击量:{{$post->cnum}}|用户:{{$post->user->name}}|赞{{$post->zans_count}}|评论{{$post->comments_count}}</p>
                    </div>
                </div>
            @endforeach
            {{ $posts->links() }}
        </div>
    </div> --}}

    <div class="col-sm-8">
        @include('post.carousel')
        <div class="list-group" style="margin-top: 1rem">
            @if ($cat)
                分类：{{ $cat->meta_title }}
            @endif
            <div class="list-group-item" style="border-radius: .5rem;">
                @foreach ($posts as $post)
                    <div class="" style="padding: 1rem;border-bottom:1px solid #eee;">
                        <div class="d-flex w-100 justify-content-between">
                            <a href="/posts/{{ $post->id }}">
                                <h5 class="mb-1">{{ $post->title }}{{ $post->id }}</h5>
                            </a>
                            <small class="text-muted">{{ $post->user->username }}</small>
                        </div>
                        <p class="">{!! Str::limit($post->content, 200, '...') !!}</p>
                    </div>
                @endforeach
            </div>
        </div>
        <div>
            {{ $posts->links() }}
        </div>

    </div>
@endsection
