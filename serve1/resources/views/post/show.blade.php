
@extends("layout.main")

@section("content")
        <div class="col-sm-8 blog-main">
            <div class="blog-post">
                <div style="display:inline-flex">
                    <h2 class="blog-post-title">{{$post->title}}
                        <small> 
                            {{-- @if (Auth::user()->can('update', $post))
                            <a style="margin: auto"  href="/posts/{{$post->id}}/edit">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                            </a> 
                            @endif
                            @if (Auth::user()->can('update', $post))                   
                            <a style="margin: auto"  href="/posts/{{$post->id}}/delete">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            </a>
                            @endif --}}
                        </small>
                    </h2>
                </div>
                <p class="blog-post-meta">{{$post->updated_at}}<a href="/user/{{$post->user->id}}">  作者:{{$post->user->username}}</a></p>
                <p class="wang">
                {!! $post->content !!}
                </p>
                {{-- <div>
                    @if($post->zan(\Auth::id())->exists())
                    <a href="/posts/{{$post->id}}/unzan" type="button" class="btn btn-default btn-lg">取消赞</a>
                    @else
                    <a href="/posts/{{$post->id}}/zan" type="button" class="btn btn-primary btn-lg">赞</a>
                    @endif
                </div> --}}
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">发表评论</div>
                <ul class="list-group">
                    <form action="/posts/comment" method="post">
                        {{csrf_field()}}
                        <input type="hidden" name="post_id" value="{{$post->id}}"/>
                        <input type="hidden" name="cat_id" value="{{$post->cat_id}}"/>
                        <li class="list-group-item">
                            <textarea name="content" class="form-control" rows="10"></textarea>
                            <button class="btn btn-default" type="submit">提交</button>
                        </li>
                    </form>
                </ul>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">评论</div>
                <ul class="list-group">
                    @foreach ($post->comments as $comment)
                    <li class="list-group-item">
                        <h5>{{$comment->created_at}} --->> {{$comment->user->username}}</h5>
                        <div>
                            {{$comment->content}}
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>


        </div><!-- /.blog-main -->
@endsection