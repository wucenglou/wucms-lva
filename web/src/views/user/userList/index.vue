<template>
    <div>
        <warning-bar title="注：右上角头像下拉可切换角色" />
        <div class="gva-table-box">
            <div class="gva-btn-list">
                <el-button type="primary" icon="plus" @click="addUser">新增用户</el-button>
                <el-select
                    v-model="optionType"
                    placeholder="请选择"
                    size="small"
                    style="width:10rem;margin-right: 1rem;margin-left: 1rem;"
                >
                    <el-option
                        v-for="item in optionsBatch"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value"
                    ></el-option>
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
                    <template #default="scope">{{ scope.row.status ? '账号正常' : '已被封禁' }}</template>
                </el-table-column>
                <el-table-column align="left" label="用户角色" min-width="150">
                    <template #default="scope">
                        <el-cascader
                            v-model="scope.row.authorityIds"
                            :options="authOptions"
                            :show-all-levels="false"
                            collapse-tags
                            :props="{ multiple: true, checkStrictly: true, label: 'authorityName', value: 'authorityId', emitPath: false }"
                            :clearable="false"
                            @visible-change="(flag) => { changeAuthority(scope.row, flag) }"
                            @remove-tag="() => { changeAuthority(scope.row, false) }"
                        />
                    </template>
                </el-table-column>
                <el-table-column align="left" label="操作" min-width="150">
                    <template #default="scope">
                        <el-popover :visible="scope.row.visible" placement="top" width="160">
                            <p>确定要删除此用户吗</p>
                            <div style="text-align: right; margin-top: 8px;">
                                <el-button type="text" @click="scope.row.visible = false">取消</el-button>
                                <el-button type="primary" @click="deleteUser(scope.row)">确定</el-button>
                            </div>
                            <template #reference>
                                <el-button type="text" icon="delete">删除</el-button>
                            </template>
                        </el-popover>
                        <el-button
                            type="text"
                            icon="magic-stick"
                            @click="resetPassword(scope.row)"
                        >重置密码</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="gva-pagination">
                <el-pagination
                    small
                    background
                    :current-page="page"
                    :page-size="pageSize"
                    :page-sizes="[10, 30, 50, 100]"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="total"
                    @current-change="handleCurrentChange"
                    @size-change="handleSizeChange"
                />
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
                <el-form-item label="真实姓名" prop="realkName">
                    <el-input v-model="userInfo.realName" />
                </el-form-item>
                <el-form-item label="用户角色" prop="authorityId">
                    <el-cascader
                        v-model="userInfo.authorityIds"
                        style="width:100%"
                        :options="authOptions"
                        :show-all-levels="false"
                        :props="{ multiple: true, checkStrictly: true, label: 'authorityName', value: 'authorityId', disabled: 'disabled', emitPath: false }"
                        :clearable="false"
                    />
                </el-form-item>
                <el-form-item label="头像" label-width="80px">
                    <div style="display:inline-block" @click="openHeaderChange">
                        <img
                            v-if="userInfo.avatarUrl"
                            class="header-img-box"
                            :src="(userInfo.avatarUrl && userInfo.avatarUrl.slice(0, 4) !== 'http') ? path + userInfo.avatarUrl : userInfo.avatarUrl"
                        />
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
// 获取列表内容封装在mixins内部  getTableData方法 初始化已封装完成
const path = import.meta.env.VITE_BASE_API
import {
    getUserList,
    setUserAuthorities,
    register,
    deleteUser,
    optionUser
} from '@/api/user'
import { getAuthorityList } from '@/api/authority'
import infoList from '@/mixins/infoList'
// import { mapGetters } from 'vuex'
import CustomPic from '@/components/customPic/index.vue'
// import ChooseImg from '@/components/chooseImg/index.vue'
import warningBar from '@/components/warningBar/warningBar.vue'
import { setUserInfo, resetPassword } from '@/api/user.js'
export default {
    name: 'Api',
    components: {
        CustomPic,
        warningBar
    },
    mixins: [infoList],
    data() {
        return {
            total: 2,
            listApi: getUserList,
            path: path,
            authOptions: [],
            addUserDialog: false,
            backNickName: '',
            userInfo: {
                username: '',
                password: '',
                realName: '',
                avatarUrl: 'https://qmplusimg.henrongyi.top/gva_header.jpg',
                authorityId: '',
                authorityIds: []
            },
            optionType: '',
            optionIds: [],
            optionsBatch: [
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
            ],
            rules: {
                username: [
                    { required: true, message: '请输入用户名', trigger: 'blur' },
                    { min: 5, message: '最低5位字符', trigger: 'blur' }
                ],
                password: [
                    { required: true, message: '请输入用户密码', trigger: 'blur' },
                    { min: 6, message: '最低6位字符', trigger: 'blur' }
                ],
                realName: [
                    { required: true, message: '请输入用户真实姓名', trigger: 'blur' }
                ],
                authorityId: [
                    { required: true, message: '请选择用户角色', trigger: 'blur' }
                ]
            }
        }
    },
    // computed: {
    //     ...mapGetters('user', ['token'])
    // },
    watch: {
        tableData() {
            this.setAuthorityIds()
        }
    },
    async created() {
        await this.getTableData()
        const res = await getAuthorityList({ page: 1, pageSize: 999 })
        console.log('77')
        console.log(res.data.list)
        this.setOptions(res.data.list)
    },
    methods: {
        async onSubmit(key) {
            console.log(this.optionType)
            console.log(this.optionIds)
            const res = await optionUser({ option: this.optionType, userIds: this.optionIds })
            if (res.code === 0) {
                this.$message({
                    type: 'success',
                    message: "操作成功"
                })
                this.setOptions(res.data.list)
            }
        },
        handleSelectionChange(val) {
            this.optionIds = []
            val.forEach(item => {
                this.optionIds.push(item.id)
            })
        },
        resetPassword(row) {
            this.$confirm(
                '是否将此用户密码重置为123456?',
                '警告',
                {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning',
                }
            ).then(async () => {
                const res = await resetPassword({
                    id: row.id,
                })
                if (res.code === 0) {
                    this.$message({
                        type: 'success',
                        message: res.msg,
                    })
                } else {
                    this.$message({
                        type: 'error',
                        message: res.msg,
                    })
                }
            })
        },
        setAuthorityIds() {
            this.tableData && this.tableData.forEach((user) => {
                const authorityIds = user.authorities && user.authorities.map(i => {
                    return i.authorityId
                })
                user.authorityIds = authorityIds
            })
        },
        openHeaderChange() {
            this.$refs.chooseImg.open()
        },
        setOptions(authData) {
            this.authOptions = []
            this.setAuthorityOptions(authData, this.authOptions)
        },
        openEidt(row) {
            if (this.tableData.some(item => item.editFlag)) {
                this.$message('当前存在正在编辑的用户')
                return
            }
            this.backNickName = row.realName
            row.editFlag = true
        },
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
        closeEdit(row) {
            row.realName = this.backNickName
            this.backNickName = ''
            row.editFlag = false
        },
        setAuthorityOptions(AuthorityData, optionsData) {
            AuthorityData &&
                AuthorityData.forEach(item => {
                    if (item.children && item.children.length) {
                        const option = {
                            authorityId: item.authorityId,
                            authorityName: item.authorityName,
                            disabled: item.disabled,
                            children: []
                        }
                        this.setAuthorityOptions(item.children, option.children)
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
        },
        async deleteUser(row) {
            const res = await deleteUser({ id: row.id })
            if (res.code === 0) {
                this.$message.success('删除成功')
                await this.getTableData()
                row.visible = false
            }
        },
        async enterAddUserDialog() {
            this.userInfo.authorityId = this.userInfo.authorityIds[0]
            this.$refs.userForm.validate(async valid => {
                if (valid) {
                    const res = await register(this.userInfo)
                    if (res.code === 0) {
                        this.$message({ type: 'success', message: '创建成功' })
                    }
                    await this.getTableData()
                    this.closeAddUserDialog()
                }
            })
        },
        closeAddUserDialog() {
            this.$refs.userForm.resetFields()
            this.userInfo.avatarUrl = ''
            this.userInfo.authorityIds = []
            this.addUserDialog = false
        },
        addUser() {
            this.addUserDialog = true
        },
        async changeAuthority(row, flag) {
            if (flag) {
                return
            }
            this.$nextTick(async () => {
                const res = await setUserAuthorities({
                    id: row.id,
                    authorityIds: row.authorityIds
                })
                if (res.code === 0) {
                    this.$message({ type: 'success', message: '角色设置成功' })
                }
            })
        },
    }
}
</script>

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
