@extends("layout.main")
@section("content")
        <div class="col-sm-8 blog-main">
            @include("post.carousel")
        <div style="height: 20px;">
            </div>
            <div>
                @foreach($posts as $post)
                <div class="panel panel-default">
                <div class="panel-body">
                    <h2 class="blog-post-title"><a href="/posts/{{$post->id}}" >{{$post->title}}</a></h2>
                    <p class="blog-post-meta">{{$post->created_at}}
                        <a href="/user/{{$post->user->id}}">
                            <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;{{$post->user->username}}
                            </button>
                        </a>
                    </p>
                    {!! Str::limit($post->content, 260,'...') !!}
                    <p class="blog-post-meta">点击量:{{$post->cnum}}|用户:{{$post->user->name}}|赞{{$post->zans_count}}|评论{{$post->comments_count}}</p>
                </div>
                </div>
                @endforeach
                {{$posts->links()}}
            </div><!-- /.blog-main -->
        </div>
@endsection