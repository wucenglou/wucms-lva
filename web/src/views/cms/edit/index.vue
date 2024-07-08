<template>
    <div>
        <div class="gva-search-box">

            <el-form ref="Form" :model="form" :rules="rules" label-width="50px">
                <el-form-item label="栏目" prop="catId">
                    <el-cascader v-model="form.catId" :options="catOption" placeholder="栏目" :props="{
                        multiple: false,
                        checkStrictly: true, label: 'metaTitle', value: 'id', disabled: 'disabled', emitPath: false
                    }" collapse-tags clearable />
                </el-form-item>

                <el-form-item label="标题" prop="title">
                    <el-input v-model="form.title"></el-input>
                </el-form-item>
                <el-form-item label="关键词" prop="keywords">
                    <el-input v-model="form.keywords"></el-input>
                </el-form-item>
                <el-form-item label="摘要" prop="description">
                    <el-input v-model="form.description" type="textarea"></el-input>
                </el-form-item>
                <el-form-item label="权重">
                    <el-input-number v-model="form.sort" :precision="2" :step="0.01" :max="10" />
                </el-form-item>

                <el-form-item label="状态" prop="status">
                    <el-radio-group v-model="form.status">
                        <el-radio label="1">已发布</el-radio>
                        <el-radio label="2">待审核</el-radio>
                        <el-radio label="3">未通过</el-radio>
                        <el-radio label="4">已废弃</el-radio>
                    </el-radio-group>
                </el-form-item>
                <!-- <el-form-item label="评论&回答">
                    <el-radio-group v-model="form.status">
                        <el-radio label="1">允许</el-radio>
                        <el-radio label="2">精选</el-radio>
                        <el-radio label="3">禁止</el-radio>
                    </el-radio-group>
                </el-form-item>-->
                <!-- <el-form-item label="属性">
                    <el-checkbox-group v-model="form.flag">
                        <el-checkbox label="1" name="type">置顶</el-checkbox>
                        <el-checkbox label="2" name="type">头条</el-checkbox>
                        <el-checkbox label="3" name="type">特荐</el-checkbox>
                        <el-checkbox label="4" name="type">精选</el-checkbox>
                    </el-checkbox-group>
                </el-form-item>-->
                <!-- <el-form-item label="发布时间">
                    <el-col :span="11">
                        <el-date-picker
                            v-model="form.date1"
                            type="date"
                            placeholder="Pick a date"
                            style="width: 100%"
                        ></el-date-picker>
                    </el-col>
                    <el-col :span="2" class="text-center">
                        <span class="text-gray-500">-</span>
                    </el-col>
                    <el-col :span="11">
                        <el-time-picker
                            v-model="form.date2"
                            placeholder="Pick a time"
                            style="width: 100%"
                        ></el-time-picker>
                    </el-col>
                </el-form-item> -->
                <!-- <el-form-item label="编辑器"> -->
                <!-- <tinymce ref="editor" v-model="form.content" @child-event="contentUrl" :disabled="disabled"
                        @onClick="onClick" /> -->
                <div style="width: 100%">
                    <editor v-model="form.content" />
                </div>

                <!-- <div style="margin-top: 10px">
                        <el-button @click="clear">清空内容</el-button>
                        <el-button @click="forbid">{{ disabled ? '已禁用' : '禁用' }}</el-button>
                    </div> -->
                <!-- </el-form-item> -->
                <div>
                    图片附件上传
                    <el-upload ref="upload" action multiple list-type="picture-card" :file-list="form.picList"
                        :on-change="change" :http-request="handleUploadForm" :auto-upload="true">
                        <el-icon>
                            <Plus />
                        </el-icon>
                        <template #file="{ file }">
                            <div>
                                <img class="el-upload-list__item-thumbnail" :src="file.url" alt="" />
                                <span class="el-upload-list__item-actions">
                                    <span class="el-upload-list__item-preview" @click="handlePictureCardPreview(file)">
                                        <el-icon>
                                            <zoom-in />
                                        </el-icon>
                                    </span>
                                    <span v-if="!disabled" class="el-upload-list__item-delete"
                                        @click="handleDownload(file)">
                                        <el-icon>
                                            <Download />
                                        </el-icon>
                                    </span>
                                    <span v-if="!disabled" class="el-upload-list__item-delete"
                                        @click="handleRemove(file)">
                                        <el-icon>
                                            <Delete />
                                        </el-icon>
                                    </span>
                                </span>
                            </div>
                        </template>
                    </el-upload>
                    <el-dialog v-model="dialogVisible">
                        <img w-full :src="dialogImageUrl" alt="Preview Image" />
                    </el-dialog>
                </div>

                <div v-if="notCheck" style="margin: 2rem;">
                    <el-button>取消</el-button>
                    <el-button type="primary" @click="onSubmit('create')">提交</el-button>
                </div>
            </el-form>
        </div>
    </div>
</template>
<script>
export default {
    name: 'Edit',
}
</script>
<script setup>
import { ref, reactive, onMounted } from 'vue'
import { getCatList } from '@/api/cat'
import { postPost, getPost } from '@/api/post'
// import tinymce from "@/components/tinymce/tinymce.vue";
import editor from "@/components/Editor/index.vue"
import { ElMessage } from 'element-plus'
import { useRouter, useRoute } from 'vue-router'
import ImagesUpload from "@/components/ImagesUpload/index.vue"
import Images from "@/components/Images/index.vue"
import { upload } from '@/api/upload'

const router = useRouter()
const route = useRoute()
const catOption = ref([])
// do not use same name with ref
const disabled = ref(false)
const form = ref({
    catId: '',
    user_id: '',
    // modeId: '',
    title: '标题',
    status: '1',
    keywords: '',
    description: '',
    content: "",
    // flag: [],
    // type: [],
    picList: [],
    sort: 0,
    // page_views: '',
})

const dialogImageUrl = ref('')
const dialogVisible = ref(false)
const handlePictureCardPreview = (file) => {
    dialogImageUrl.value = file.url
    dialogVisible.value = true
}

const formData = new FormData()
const handleUploadForm = (file) => {
    // console.log(file)
    formData.append("files[]", file.file);
    // formData.append("id", 1);
    // form.append("picList[]", file.file)
    // upload(formData).then(res => {
    // fileList.value = [res.data]
    // console.log(fileList.value)
    // })
    // form.value.picList.push(file)
}

const handleRemove = (file) => {
    for (let i = 0; i < form.value.picList.length; i++) {
        if (form.value.picList[i] === file) {
            upload({ "deleteid": file.id }).then(res => {
                if (!res['code']) {
                    form.value.picList.splice(i)
                    ElMessage({
                        type: 'success',
                        message: res.msg
                    })
                }
            })
        }
    }
}

const handleDownload = (file) => {
    console.log(file)
}

// const filelist = new FormData();
const change = (file) => {
    // form.value.picList.push(file)
    // const filelist = new FormData();
    // console.log(form.value.picList)
    // console.log(file)
}

// 查询
const tableDataCat = ref([])
const notCheck = ref(true)
const getTableData = async () => {
    console.log(route.params)
    if (route.params && route.params.post_id) {
        const formData = await getPost(route.params)

        console.log(formData.data[0])
        if (formData.code === 0) {
            form.value = formData.data[0]

            // form.value.picList = []
        }
    }
    const tableCat = await getCatList()
    if (tableCat.code === 0) {
        tableDataCat.value = tableCat.data.list
    }
    setModeCatOptions()
    if (route.params.type && route.params.type == 'check') {
        notCheck.value = false
    }
    console.log('create')
}
getTableData()

const setModeCatOptions = () => {
    setOptions(tableDataCat.value, catOption.value, false)
}

const setOptions = (modeData, optionsData, disabled) => {
    modeData &&
        modeData.forEach(item => {
            if (item.children && item.children.length) {
                const option = {
                    id: String(item.id),
                    name: item.name,
                    metaTitle: item.metaTitle ? item.metaTitle : '',
                    // disabled: disabled || item.id ,
                    children: []
                }
                setOptions(
                    item.children,
                    option.children,
                    disabled || item.id
                )
                optionsData.push(option)
            } else {
                const option = {
                    id: String(item.id),
                    name: item.name,
                    metaTitle: item.metaTitle ? item.metaTitle : '',
                    // disabled: disabled || item.id 
                }
                optionsData.push(option)
            }
        })
}

// 表单验证规则
const rules = ref({
    catId: [
        { required: true, message: '请选择栏目', trigger: 'blur' },
    ],
    title: [
        { required: true, message: '请输入标题', trigger: 'blur' },
        { min: 2, message: '最低2位字符', trigger: 'blur' }
    ],
    sort: [
        { type: 'number', message: '必须为数字' }
    ]
})

// 编辑器配置
const contentUrl = () => {
    console.log(url);
}
const onClick = (e, editor) => {
    console.log("Element clicked");
    console.log(e);
    console.log(editor);
}
const clear = () => {
    form.value.content = ''
}
const forbid = () => {
    disabled.value = !disabled.value
}
const Form = ref(null)
const onSubmit = async (key, id, row) => {
    Form.value.validate(async valid => {
        if (valid) {
            switch (key) {
                case 'create':
                    formData.append("form", JSON.stringify(form.value))
                    const res = await postPost(formData)
                    if (res.code === 0) {
                        ElMessage({
                            type: 'success',
                            message: '添加成功',
                            showClose: true
                        })
                        console.log("--------------------")
                        router.push({ name: 'content' })
                    }
                    console.log(form.value)
                    break
                // case 'edit':
                //     dialogTitle.value = '编辑栏目'
                //     dialogType.value = 'edit'
                //     break
                default:
                    break
            }
        }

    })
}
</script>
            
