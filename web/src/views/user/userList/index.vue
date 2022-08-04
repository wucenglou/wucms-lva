<template>
    <div>
        <warning-bar title="注：右上角头像下拉可切换角色" />
        <div class="gva-search-box">
            <el-form ref="searchForm" :inline="true" :model="searchInfo">
                <el-form-item label="选择角色">
                    <el-cascader v-model="searchInfo.authorityId" :options="authOptions"
                        :props="{ multiple: true, checkStrictly: true, label: 'authorityName', value: 'authorityId', emitPath: false }"
                        :show-all-levels="false" @change="BlurChange" @visible-change="Blur" filterable />
                </el-form-item>
                <el-form-item label="选择状态">
                    <el-cascader v-model="searchInfo.status" :options="statusData" placeholder="状态" @change="BlurChange"
                        @visible-change="Blur"
                        :props="{ checkStrictly: true, label: 'label', value: 'value', disabled: 'disabled', emitPath: false }"
                        collapse-tags clearable />
                </el-form-item>
                <el-form-item label="搜索用户">
                    <el-input v-model="searchInfo.value" placeholder="搜索" />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" icon="search" @click="onSubmit('search')">查询</el-button>
                    <el-button icon="refresh">重置</el-button>
                </el-form-item>
            </el-form>
        </div>
        <div class="gva-table-box">
            <div class="gva-btn-list">
                <el-button type="primary" icon="plus" @click="addUser">新增用户</el-button>
                <el-select v-model="optionType" placeholder="请选择" size="small"
                    style="width:10rem;margin-right: 1rem;margin-left: 1rem;">
                    <el-option v-for="item in optionsBatch" :key="item.value" :label="item.label" :value="item.value">
                    </el-option>
                </el-select>
                <el-button @click="onSubmit('batch', 0)" type="primary">批量操作</el-button>
            </div>
            <el-table :data="tableData" border @selection-change="handleSelectionChange">
                <el-table-column type="selection" width="40" />
                <el-table-column align="left" label="头像" min-width="50">
                    <template #default="scope">
                        <CustomPic style="margin-top:8px" :pic-src="scope.row.avatarUrl" />
                    </template>
                </el-table-column>

                <el-table-column align="left" label="ID" min-width="50" prop="id" />
                <!-- <el-table-column align="left" label="UUID" min-width="160" prop="uuid" /> -->
                <el-table-column align="left" label="用户名" min-width="150" prop="username" />
                <el-table-column align="left" label="真实姓名" min-width="100" prop="realName">
                    <template #default="scope">
                        <p v-if="!scope.row.editFlag" class="nickName">
                            {{ scope.row.realName }}
                            <el-icon class="pointer" color="#66b1ff" @click="openEidt(scope.row)">
                                <edit />
                            </el-icon>
                        </p>
                        <p v-if="scope.row.editFlag" class="nickName">
                            <el-input v-model="scope.row.realName" />
                            <el-icon class="pointer" color="#67c23a" @click="enterEdit(scope.row)">
                                <check />
                            </el-icon>
                            <el-icon class="pointer" color="#f23c3c" @click="closeEdit(scope.row)">
                                <close />
                            </el-icon>
                        </p>
                    </template>
                </el-table-column>
                <el-table-column align="left" label="状态" min-width="150" prop="status">
                    <template #default="scope">{{ scope.row.status == 1 ? '账号正常' : '已被封禁' }}</template>
                </el-table-column>
                <el-table-column align="left" label="用户角色" min-width="150">
                    <template #default="scope">
                        <el-cascader v-model="scope.row.authorityIds" :options="authOptions" :show-all-levels="false"
                            collapse-tags
                            :props="{ multiple: true, checkStrictly: true, label: 'authorityName', value: 'authorityId', emitPath: false }"
                            :clearable="false" @visible-change="(flag) => { changeAuthority(scope.row, flag) }"
                            @remove-tag="() => { changeAuthority(scope.row, false) }" />
                    </template>
                </el-table-column>
                <el-table-column align="left" label="操作" min-width="190">
                    <template #default="scope">
                        <el-popover :visible="scope.row.visible" placement="top" width="160">
                            <p>确定要删除此用户吗</p>
                            <div style="text-align: right; margin-top: 8px;">
                                <el-button type="primary" @click="scope.row.visible = false">取消</el-button>
                                <el-button type="primary" @click="deleteUserFunc(scope.row)">确定</el-button>
                            </div>
                            <template #reference>
                                <el-button type="primary" icon="delete">删除</el-button>
                            </template>
                        </el-popover>
                        <el-button type="primary" icon="magic-stick" @click="resetPasswordFunc(scope.row)">重置密码
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="gva-pagination">
                <el-pagination small background :current-page="page" :page-size="pageSize"
                    :page-sizes="[10, 30, 50, 100]" layout="total, sizes, prev, pager, next, jumper" :total="total"
                    @current-change="handleCurrentChange" @size-change="handleSizeChange" />
            </div>
        </div>
        <el-dialog v-model="addUserDialog" custom-class="user-dialog" title="新增用户">
            <el-form ref="userForm" :rules="rules" :model="userInfo" label-width="80px">
                <el-form-item label="用户名" prop="username">
                    <el-input v-model="userInfo.username" />
                </el-form-item>
                <el-form-item label="密码" prop="password">
                    <el-input v-model="userInfo.password" />
                </el-form-item>
                <el-form-item label="真实姓名" prop="realName">
                    <el-input v-model="userInfo.realName" />
                </el-form-item>
                <el-form-item label="用户角色" prop="authorityId">
                    <el-cascader v-model="userInfo.authorityIds" style="width:100%" :options="authOptions"
                        :show-all-levels="false"
                        :props="{ multiple: true, checkStrictly: true, label: 'authorityName', value: 'authorityId', disabled: 'disabled', emitPath: false }"
                        :clearable="false" />
                </el-form-item>
                <el-form-item label="头像" label-width="80px">
                    <div style="display:inline-block" @click="openHeaderChange">
                        <img v-if="userInfo.avatarUrl" class="header-img-box"
                            :src="(userInfo.avatarUrl && userInfo.avatarUrl.slice(0, 4) !== 'http') ? path + userInfo.avatarUrl : userInfo.avatarUrl" />
                        <div v-else class="header-img-box">从媒体库选择</div>
                    </div>
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button size="small" @click="closeAddUserDialog">取 消</el-button>
                    <el-button size="small" type="primary" @click="enterAddUserDialog">确 定</el-button>
                </div>
            </template>
        </el-dialog>
        <!-- <ChooseImg ref="chooseImg" :target="userInfo" :target-key="`avatarUrl`" /> -->
    </div>
</template>

<script>
export default {
    name: 'userList'
}
</script>

<script setup>
import {
    getUserList,
    setUserAuthorities,
    register,
    deleteUser,
    optionUser
} from '@/api/user'
import { getAuthorityList } from '@/api/authority'
// import { mapGetters } from 'vuex'
import CustomPic from '@/components/customPic/index.vue'
// import ChooseImg from '@/components/chooseImg/index.vue'
import warningBar from '@/components/warningBar/warningBar.vue'
import { setUserInfo, resetPassword } from '@/api/user.js'
import { ref, watch, nextTick } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'

const rules = ref({
    username: [
        { required: true, message: '请输入用户名', trigger: 'blur' },
        { min: 5, message: '最低5位字符', trigger: 'blur' }
    ],
    password: [
        { required: true, message: '请输入用户密码', trigger: 'blur' },
        { min: 6, message: '最低6位字符', trigger: 'blur' }
    ],
    realName: [
        { required: false, message: '请输入用户真实姓名', trigger: 'blur' },
    ],
    authorityId: [
        { required: true, message: '请选择用户角色', trigger: 'blur' }
    ]
})
const userInfo = ref({
    username: '',
    password: '',
    realName: '',
    avatarUrl: 'https://qmplusimg.henrongyi.top/gva_header.jpg',
    authorityId: '',
    authorityIds: []
})
const optionsBatch = ref([
    {
        value: '0',
        label: '封禁',
    },
    {
        value: '1',
        label: '解封',
    },
    {
        value: 'Delete',
        label: '删除',
    },
])

const statusData = ref([
    {
        value: 1,
        label: '正常状态',
    },
    {
        value: 2,
        label: '封禁状态',
    }
])

const page = ref(1)
const total = ref(0)
const pageSize = ref(10)
const tableData = ref([])
const searchInfo = ref({})
// 分页
const handleSizeChange = (val) => {
    pageSize.value = val
    getTableData
}

const handleCurrentChange = (val) => {
    page.value = val
    getTableData()
}
// 查询
const getTableData = async () => {
    const table = await getUserList({ page: page.value, pageSize: pageSize.value, ...searchInfo.value })
    console.log(table)
    if (table.code === 0) {
        tableData.value = table.data.list
        total.value = table.data.total
        page.value = table.data.page
        pageSize.value = table.data.pageSize
    }
}

watch(() => tableData.value, () => {
    setAuthorityIds()
})

const setAuthorityIds = () => {
    tableData.value && tableData.value.forEach((user) => {
        const authorityIds = user.authorities && user.authorities.map(i => {
            return i.authorityId
        })
        user.authorityIds = authorityIds
    })
}

const initPage = async () => {
    getTableData()
    const res = await getAuthorityList({ page: 1, pageSize: 999 })
    searchInfo.value.authorities =
    console.log(res)
    setOptions(res.data.list)
}

initPage()

const authOptions = ref([])
const setOptions = (authData) => {
    authOptions.value = []
    setAuthorityOptions(authData, authOptions.value)
}

const setAuthorityOptions = (AuthorityData, optionsData) => {
    AuthorityData &&
        AuthorityData.forEach(item => {
            if (item.children && item.children.length) {
                const option = {
                    authorityId: item.authorityId,
                    authorityName: item.authorityName,
                    disabled: item.disabled,
                    children: []
                }
                setAuthorityOptions(item.children, option.children)
                optionsData.push(option)
            } else {
                const option = {
                    authorityId: item.authorityId,
                    authorityName: item.authorityName,
                    disabled: item.disabled,
                }
                optionsData.push(option)
            }
        })
}

const addUserDialog = ref(false)
const addUser = () => {
    addUserDialog.value = true
}
const userForm = ref(null)
const enterAddUserDialog = async () => {
    userInfo.value.authorityId = userInfo.value.authorityIds[0]
    userForm.value.validate(async valid => {
        if (valid) {
            const res = await register(userInfo.value)
            if (res.code === 0) {
                ElMessage({
                    type: 'success',
                    message: '创建成功'
                })
            }
            await getTableData()
            closeAddUserDialog()
        }
    })
}

const closeAddUserDialog = () => {
    userForm.value.resetFields()
    userInfo.value.avatarUrl = ''
    userInfo.value.authorityIds = []
    addUserDialog.value = false
}

const optionType = ref('')

const onSubmit = async (key) => {
    console.log(optionType.value)
    console.log(optionIds.value)
    const res = await optionUser({ option: optionType.value, userIds: optionIds.value })
    if (res.code === 0) {
        ElMessage({
            type: 'success',
            message: "操作成功"
        })
    }
    initPage()
}

const optionIds = ref([])
const handleSelectionChange = (val) => {
    optionIds.value = []
    val.forEach(item => {
        optionIds.value.push(item.id)
    })
}

// 改变角色
const changeAuthority = async (row, flag) => {
    if (flag) {
        return
    }
    await nextTick()
    const res = await setUserAuthorities({
        id: row.id,
        authorityIds: row.authorityIds
    })
    if (res.code === 0) {
        ElMessage({ type: 'success', message: '角色设置成功' })
    }
}

// 重置密码
const resetPasswordFunc = (row) => {
    ElMessageBox.confirm(
        '是否将此用户密码重置为123456?',
        '警告',
        {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
        }
    ).then(async () => {
        const res = await resetPassword({
            ID: row.ID,
        })
        console.log(res)
        if (res.code === 0) {
            ElMessage({
                type: 'success',
                message: res.msg,
            })
        } else {
            // ElMessage({
            //     type: 'error',
            //     message: res.msg,
            // })
        }
    })
}
// 编辑昵称/姓名
const backNickName = ref('')
const openEidt = (row) => {
    if (tableData.value.some(item => item.editFlag)) {
        ElMessage('当前存在正在编辑的用户')
        return
    }
    backNickName.value = row.realName
    row.editFlag = true
}
const closeEdit = (row) => {
    row.realName = backNickName.value
    backNickName.value = ''
    row.editFlag = false
}

const deleteUserFunc = async (row) => {
    const res = await deleteUser({ id: row.id })
    if (res.code === 0) {
        ElMessage({ message: '删除成功', type: 'success' })
        await getTableData()
        row.visible = false
    }
}

</script>


<!-- <script>
const path = import.meta.env.VITE_BASE_API
export default {
    data() {
        return {
            path: path,
            authOptions: [],
            backNickName: '',
        }
    },
    methods: {
        
        
        openHeaderChange() {
            this.$refs.chooseImg.open()
        },
        ,
        ,
        async enterEdit(row) {
            const res = await setUserInfo({ realName: row.realName, id: row.id })
            if (res.code === 0) {
                this.$message({
                    type: 'success',
                    message: '设置成功'
                })
            }
            this.backNickName = ''
            row.editFlag = false
        },
    }
}
</script> -->

<style lang="scss">
.user-dialog {
    .header-img-box {
        width: 200px;
        height: 200px;
        border: 1px dashed #ccc;
        border-radius: 20px;
        text-align: center;
        line-height: 200px;
        cursor: pointer;
    }

    .avatar-uploader .el-upload:hover {
        border-color: #409eff;
    }

    .avatar-uploader-icon {
        border: 1px dashed #d9d9d9 !important;
        border-radius: 6px;
        font-size: 28px;
        color: #8c939d;
        width: 178px;
        height: 178px;
        line-height: 178px;
        text-align: center;
    }

    .avatar {
        width: 178px;
        height: 178px;
        display: block;
    }
}

.nickName {
    display: flex;
    justify-content: flex-start;
    align-items: center;
}

.pointer {
    cursor: pointer;
    font-size: 16px;
    margin-left: 2px;
}
</style>
