<script>
export default {
    name: 'Editor',
}
</script>
<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import tinymce from 'tinymce/tinymce'
import TinymceEditor from '@tinymce/tinymce-vue'
import 'tinymce/themes/silver/theme'
import 'tinymce/icons/default/icons'
import 'tinymce/models/dom'
import 'tinymce/plugins/autolink'
import 'tinymce/plugins/autoresize'
import 'tinymce/plugins/fullscreen'
import 'tinymce/plugins/image'
import 'tinymce/plugins/insertdatetime'
import 'tinymce/plugins/link'
import 'tinymce/plugins/lists'
import 'tinymce/plugins/media'
import 'tinymce/plugins/preview'
import 'tinymce/plugins/table'
import 'tinymce/plugins/wordcount'
import 'tinymce/plugins/code'
import 'tinymce/plugins/searchreplace'
import 'tinymce/plugins/quickbars'; //快速工具栏

import { upload } from '@/api/upload'
// import useSettingsStore from '@/store/modules/settings'

// const settingsStore = useSettingsStore()

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    setting: {
        type: Object,
        default: () => { }
    },
    disabled: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue'])

const defaultSetting = ref({
    language_url: 'tinymce/langs/zh-Hans.js',
    language: 'zh-Hans',
    skin_url: 'tinymce/skins/ui/oxide',
    content_css: 'tinymce/skins/content/default/content.min.css',
    min_height: 450,
    max_height: 600,
    selector: 'textarea',
    plugins: 'autolink autoresize fullscreen image insertdatetime link lists media preview table wordcount code searchreplace',
    toolbar: 'undo redo | blocks | bold italic underline strikethrough blockquote | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor removeformat | link image media table insertdatetime searchreplace | preview code fullscreen',
    branding: false,
    menubar: false,
    toolbar_mode: 'sliding',
    insertdatetime_formats: [
        '%Y年%m月%d日',
        '%H点%M分%S秒',
        '%Y-%m-%d',
        '%H:%M:%S'
    ],
    images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
        var formData;
        formData = new FormData();
        formData.append("file", blobInfo.blob(), blobInfo.filename());
        upload(formData).then(res => {
            resolve(res.data.location)
        })
    }),
})
// console.log(blobInfo)



const myValue = ref(props.modelValue)

const completeSetting = computed(() => {
    return Object.assign(defaultSetting.value, props.setting)
})

watch(() => myValue.value, newValue => {
    emit('update:modelValue', newValue)
})

watch(() => props.modelValue, newValue => {
    myValue.value = newValue
})

onMounted(() => {
    tinymce.init({})
})
</script>

<template>
    <div class="editor">
        <TinymceEditor v-model="myValue" :init="completeSetting" :disabled="disabled" />
    </div>
</template>

<style lang="scss" scoped>
:deep(.tox-tinymce) {
    border-radius: 4px;
}
</style>
