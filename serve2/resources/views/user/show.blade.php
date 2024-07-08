@extends('layout.main')

@section('content')
    <div class="col-sm-8">
        <blockquote>
            <p>
                <img src="{{ $user->avatar_url }}" alt="" class="img-rounded" style="border-radius:500px; height: 40px">
                {{ $user->username }}
            </p>
            <footer>关注：{{ $user->stars_count }}｜粉丝：{{ $user->fans_count }}｜文章：{{ $user->posts_count }}</footer>
            @include('user.badges.like', ['target_user' => $user])
        </blockquote>
    </div>
    <div class="col-sm-8 blog-main">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button"
                    role="tab" aria-controls="home" aria-selected="true">文章</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button"
                    role="tab" aria-controls="profile" aria-selected="false">关注</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button"
                    role="tab" aria-controls="contact" aria-selected="false">粉丝</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                @foreach ($posts as $post)
                    <div class="blog-post" style="margin-top: 30px">
                        <?php \Carbon\Carbon::setLocale('zh'); ?>
                        <p class=""><a href="/user/{{ $post->user_id }}">{{ $post->user->name }}</a>
                            {{ $post->created_at->diffForHumans() }}</p>
                        <p class=""><a href="/posts/{{ $post->id }}">{{ $post->title }}</a></p>
                        <p>{!! Str::limit($post->content, 100, '...') !!}</p>
                    </div>
                @endforeach
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                @foreach ($stars as $star)
                    <?php $suser = $star->suser()->first(); ?>
                    <div class="blog-post" style="margin-top: 30px">
                        <p class=""><a href="/user/{{ $suser->id }}">{{ $suser->username }}</a></p>
                        <p class="">关注：{{ $suser->stars()->count() }} | 粉丝：{{ $suser->fans()->count() }}｜
                            文章：{{ $suser->posts()->count() }}</p>

                        @include('user.badges.like', ['target_user' => $suser])
                    </div>
                @endforeach
            </div>
            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                @foreach ($fans as $fan)
                    <?php $fuser = $fan->fuser()->first(); ?>
                    <div class="blog-post" style="margin-top: 30px">
                        <p class=""><a href="/user/{{ $fuser->id }}">{{ $fuser->username }}</a></p>
                        <p class="">关注：{{ $fuser->stars()->count() }} | 粉丝：{{ $fuser->fans()->count() }}｜
                            文章：{{ $fuser->posts()->count() }}</p>

                        @include('user.badges.like', ['target_user' => $fuser])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
