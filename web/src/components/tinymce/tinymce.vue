<template>
    <div class="tinymce-box">
        <editor v-model="myValue" :init="init" :disabled="disabled"></editor>
    </div>
</template>

<script>
export default {
    name: 'tinymce',
}
</script>

<script setup>
import { ref, defineEmits, onMounted, watch } from 'vue';
import Editor from "@tinymce/tinymce-vue";
import tinymce from "tinymce/tinymce"; //tinymce默认hidden，不引入不显示
import "tinymce/themes/silver";
import 'tinymce/icons/default'; //引入编辑器图标icon，不引入则不显示对应图标
// 编辑器插件plugins
// 更多插件参考：https://www.tiny.cloud/docs/plugins/
// 引入编辑器插件
import 'tinymce/plugins/advlist'; //高级列表
import 'tinymce/plugins/anchor'; //锚点
import 'tinymce/plugins/autolink'; //自动链接
import 'tinymce/plugins/autoresize'; //编辑器高度自适应,注：plugins里引入此插件时，Init里设置的height将失效
import 'tinymce/plugins/autosave'; //自动存稿
import 'tinymce/plugins/charmap'; //特殊字符
import 'tinymce/plugins/code'; //编辑源码
import 'tinymce/plugins/codesample'; //代码示例
import 'tinymce/plugins/directionality'; //文字方向
import 'tinymce/plugins/emoticons'; //表情
import 'tinymce/plugins/fullpage'; //文档属性
import 'tinymce/plugins/fullscreen'; //全屏
import 'tinymce/plugins/help'; //帮助
import 'tinymce/plugins/hr'; //水平分割线
import 'tinymce/plugins/image'; //插入编辑图片
import 'tinymce/plugins/importcss'; //引入css
import 'tinymce/plugins/insertdatetime'; //插入日期时间
import 'tinymce/plugins/link'; //超链接
import 'tinymce/plugins/lists'; //列表插件
import 'tinymce/plugins/media'; //插入编辑媒体
import 'tinymce/plugins/nonbreaking'; //插入不间断空格
import 'tinymce/plugins/pagebreak'; //插入分页符
import 'tinymce/plugins/paste'; //粘贴插件
import 'tinymce/plugins/preview'; //预览
import 'tinymce/plugins/print'; //打印
import 'tinymce/plugins/quickbars'; //快速工具栏
import 'tinymce/plugins/save'; //保存
import 'tinymce/plugins/searchreplace'; //查找替换
// import 'tinymce/plugins/spellchecker'  //拼写检查，暂未加入汉化，不建议使用
import 'tinymce/plugins/tabfocus'; //切入切出，按tab键切出编辑器，切入页面其他输入框中
import 'tinymce/plugins/table'; //表格
import 'tinymce/plugins/template'; //内容模板
import 'tinymce/plugins/textcolor'; //文字颜色
import 'tinymce/plugins/textpattern'; //快速排版
import 'tinymce/plugins/toc'; //目录生成器
import 'tinymce/plugins/visualblocks'; //显示元素范围
import 'tinymce/plugins/visualchars'; //显示不可见字符
import 'tinymce/plugins/wordcount'; //字数统计
import 'tinymce/plugins/imagetools'; //
// import 'tinymce/plugins/bdmap'; //
import 'tinymce/plugins/emoticons/js/emojis'; //

const myValue = ref()
const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    my: {
        type: String,
        default: "",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    plugins: {
        type: [String, Array],
        default: "emoticons help quickbars autosave imagetools print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template code codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount textpattern autosave ",
    },
    toolbar: {
        type: [String, Array],
        default:
            "undo redo restoredraft code| cut copy paste pastetext | forecolor backcolor bold italic underline strikethrough link anchor | alignleft aligncenter alignright alignjustify outdent indent | \
        styleselect formatselect fontselect fontsizeselect | bullist numlist | blockquote subscript superscript removeformat | \
        table image media charmap emoticons hr pagebreak insertdatetime print preview | fullscreen | bdmap indent2em lineheight formatpainter axupimgs importword kityformula-editor",
    },
    emits: ['update:modelValue'],
})

const init = ref({
    selector: "textarea",
    language_url: "/tinymce/langs/zh_CN.js",
    language: "zh_CN",
    skin_url: "/tinymce/skins/ui/oxide",
    // skin_url: 'tinymce/skins/ui/oxide-dark',//暗色系
    content_css: 'tinymce/skins/content/default/content.css',
    height: 500,
    toolbar_mode: 'wrap',
    plugins: props.plugins,
    toolbar: props.toolbar,
    branding: false,
    menubar: true,
    image_caption: true,
    automatic_uploads: true,
    deprecation_warnings: false,
    // 此处为图片上传处理函数，这个直接用了base64的图片形式上传图片，
    // 如需ajax上传可参考https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_handler
    // eslint-disable-next-line no-unused-vars
    // images_upload_handler: (blobInfo, success, failure) => {
    //   const img = "data:image/jpeg;base64," + blobInfo.base64();
    //   success(img);
    // },
    images_upload_handler: function (blobInfo, success, failure, progress) {
        var xhr, formData;
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open("POST", "/api/uploadimgs");

        xhr.upload.onprogress = function (e) {
            progress((e.loaded / e.total) * 100);
        };

        xhr.onload = function () {
            var json;
            if (xhr.status < 200 || xhr.status >= 300) {
                failure("HTTP Error: " + xhr.status);
                return;
            }

            json = JSON.parse(xhr.responseText);

            if (!json || typeof json.location != "string") {
                failure("Invalid JSON: " + xhr.responseText);
                return;
            }

            success(json.location);
        };

        xhr.onerror = function () {
            failure(
                "Image upload failed due to a XHR Transport error. Code: " +
                xhr.status
            );
        };

        formData = new FormData();
        formData.append("file", blobInfo.blob(), blobInfo.filename());

        xhr.send(formData);
    },
})

watch(() => props.modelValue, (newValue) => {
    myValue.value = newValue
})
const emit = defineEmits()
watch(
    () => myValue.value,
    (newValue) => {
        emit("update:modelValue", newValue);
    },
)

</script>