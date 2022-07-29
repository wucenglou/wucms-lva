@extends("layout.main")

@section("content")
    <div class="col-sm-8 blog-main">
        <form class="form-horizontal" action="/user/{{\Auth::id()}}/setting" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <div class="form-group">
                <label class="col-sm-2 control-label">用户名</label>
                <div class="col-sm-10">
                    <input class="form-control" name="username" type="text" value="{{$me->username}}">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">头像</label>
                <div class="col-sm-2">
                    <img  class="img-thumbnail" src="{{$me->avatar_url}}" alt="" class="img-rounded" style="border-radius:500px;">
                    <input class=" file-loading preview_input" type="file" value="用户名" style="width:72px" name="avatar_url">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
            <button type="submit" class="btn btn-default">确认修改</button>
            </div>
        </form>
        <br>
    </div>
@endsection