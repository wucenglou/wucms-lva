$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
})

// var editor = new wangEditor('content');
// if(editor.config){
//     // 上传图片（举例）
//     editor.config.uploadImgUrl = '/posts/image/upload';
    
//     // 设置 headers（举例）
//     editor.config.uploadHeaders = {
//         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//     };
// editor.create();
// }

$(".preview_input").change(function(event){
    var file = event.currentTarget.files[0];
    var url = window.URL.createObjectURL(file);
    $(event.target).next(".preview_img").attr("src", url);
});

$(".like-button").click(function(event){
    //console.log(event);
    target = $(event.target);
    console.log(target);
    var current_like = target.attr('like-value');
    var user_id = target.attr("like-user");
    if (current_like == 1) {
        //取消关注
        $.ajax({
            url: "/user/" + user_id + "/unfan",
            method: "POST",
            data: "json",
            success: function success(data) {
                if (data.error != 0) {
                    alert(data.msg);
                }
                target.attr("like-value", 0);
                target.text("关注");
            }
        });
    } else {
        //关注
        $.ajax({
            url: "/user/" + user_id + "/fan",
            method: "POST",
            data: "json",
            success: function success(data) {
                if (data.error != 0) {
                    alert(data.msg);
                }
                target.attr("like-value", 0);
                target.text("取消关注");
            }
        });
    }
    // console.log(current_like);
    // console.log(user_id);

});

$(".panel-body img").css("width","100%");
$(".blog-post img").css("width","100%");