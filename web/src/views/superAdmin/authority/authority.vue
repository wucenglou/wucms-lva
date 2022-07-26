<template>
  <div class="authority">
    <warning-bar title="注：右上角头像下拉可切换角色" />
    <div class="gva-table-box">
      <div class="gva-btn-list">
        <el-button type="primary" icon="plus" @click="addAuthority('0')">新增角色</el-button>
      </div>

      <el-table :data="tableData" border :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
        row-key="authorityId" style="width: 100%">
        <el-table-column label="角色ID" min-width="40" prop="authorityId" />
        <el-table-column align="left" label="角色名称" min-width="100" prop="authorityName" />
        <el-table-column align="left" label="系统用户" min-width="50" prop="authoritySys">
          <template #default="scope">
            <span>{{ scope.row.authoritySys ? "是" : "否" }}</span>
          </template>
        </el-table-column>
        <el-table-column align="left" label="角色描述" min-width="100" prop="authorityDescribe" />
        <el-table-column align="left" label="操作" width="450">
          <template #default="scope">
            <el-button icon="setting" type="primary" @click="opdendrawer(scope.row)">设置权限</el-button>
            <el-button icon="plus" type="primary" @click="addAuthority(scope.row.authorityId)">新增子角色</el-button>
            <el-button icon="copy-document" type="primary" @click="copyAuthorityFunc(scope.row)">拷贝</el-button>
            <el-button icon="edit" type="primary" @click="editAuthority(scope.row)">编辑</el-button>
            <el-button icon="delete" type="primary" @click="deleteAuth(scope.row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <!-- 新增角色弹窗 -->
    <el-dialog v-model="dialogFormVisible" :title="dialogTitle">
      <el-form ref="authorityForm" :model="form" :rules="rules" label-width="80px">
        <el-form-item label="父级角色" prop="parentId">
          <el-cascader v-model="form.parentId" style="width:100%" :disabled="dialogType == 'add'"
            :options="AuthorityOption"
            :props="{ checkStrictly: true, label: 'authorityName', value: 'authorityId', disabled: 'disabled', emitPath: false }"
            :show-all-levels="false" filterable />
        </el-form-item>
        <el-form-item label="角色名称" prop="authorityName">
          <el-input v-model="form.authorityName" autocomplete="off" />
        </el-form-item>
        <el-form-item label="系统角色" prop="authoritySys">
          <el-switch v-model="form.authoritySys" :active-value="1" :inactive-value="0"></el-switch>
        </el-form-item>
        <el-form-item label="角色描述" prop="authorityDescribe">
          <el-input v-model="form.authorityDescribe" :autosize="{ minRows: 2, maxRows: 4 }" type="textarea"
            autocomplete="off" />
        </el-form-item>
      </el-form>
      <template #footer>
        <div class="dialog-footer">
          <el-button size="small" @click="closeDialog">取 消</el-button>
          <el-button size="small" type="primary" @click="enterDialog">确 定</el-button>
        </div>
      </template>
    </el-dialog>

    <el-drawer v-if="drawer" v-model="drawer" :with-header="false" size="40%" title="角色配置">
      <el-tabs :before-leave="autoEnter" class="role-box" type="border-card">
        <el-tab-pane label="角色菜单">
          <Menus ref="menus" :row="activeRow" @changeRow="changeRow" />
        </el-tab-pane>
        <el-tab-pane label="角色api">
          <Apis ref="apis" :row="activeRow" @changeRow="changeRow" />
        </el-tab-pane>
        <!-- <el-tab-pane label="资源权限">
          <Datas ref="datas" :authority="tableData" :row="activeRow" @changeRow="changeRow" />
        </el-tab-pane>-->
      </el-tabs>
    </el-drawer>
  </div>
</template>

<script>
export default {
  name: 'Authority'
}
</script>

<script setup>
import {
  getAuthorityList,
  deleteAuthority,
  createAuthority,
  updateAuthority,
  copyAuthority
} from '@/api/authority'
import { ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import Menus from '@/views/superAdmin/authority/components/menus.vue'
import Apis from '@/views/superAdmin/authority/components/apis.vue'
// import Datas from '@/views/superAdmin/authority/components/datas.vue'
import warningBar from '@/components/warningBar/warningBar.vue'

const mustUint = (rule, value, callback) => {
  if (!/^[0-9]*[1-9][0-9]*$/.test(value)) {
    return callback(new Error('请输入正整数'))
  }
  return callback()
}

const AuthorityOption = ref([
  {
    authorityId: '0',
    authorityName: '根角色'
  }
])

const drawer = ref(false)
const dialogType = ref('add')
const activeRow = ref({})

const dialogTitle = ref('新增角色')
const dialogFormVisible = ref(false)
const apiDialogFlag = ref(false)
const copyForm = ref({})

const form = ref({
  // authorityId: '',
  authorityName: '',
  authoritySys: 1,
  authorityDescribe: '',
  parentId: '0'
})
const rules = ref({
  // authorityId: [
  //   { required: true, message: '请输入角色ID', trigger: 'blur' },
  //   { validator: mustUint, trigger: 'blur' }
  // ],
  authorityName: [
    { required: true, message: '请输入角色名', trigger: 'blur' }
  ],
  parentId: [
    { required: true, message: '请选择请求方式', trigger: 'blur' }
  ]
})

const page = ref(1)
const total = ref(0)
const pageSize = ref(999)
const tableData = ref([])
const searchInfo = ref({})

// 查询
const getTableData = async() => {
  const table = await getAuthorityList({ page: page.value,pageSize: pageSize.value, ...searchInfo.value })
  if (table.code === 0) {
    tableData.value = table.data.list
    total.value = table.data.total
    page.value = table.data.page
    pageSize.value = table.data.pageSize
  }
}

getTableData()

const changeRow = (key, value) => {
  activeRow.value[key] = value
}
const menus = ref(null)
const apis = ref(null)
const datas = ref(null)

const autoEnter = (activeName, oldActiveName) => {
  const paneArr = [menus, apis, datas]
  if (oldActiveName) {
    if (paneArr[oldActiveName].value.needConfirm) {
      paneArr[oldActiveName].value.enterAndNext()
      paneArr[oldActiveName].value.needConfirm = false
    }
  }
}
// 拷贝角色
const copyAuthorityFunc = (row) => {
  setOptions()
  dialogTitle.value = '拷贝角色'
  dialogType.value = 'copy'
  for (const k in form.value) {
    form.value[k] = row[k]
  }
  copyForm.value = row
  dialogFormVisible.value = true
}
const opdendrawer = (row) => {
  drawer.value = true
  activeRow.value = row
}
// 删除角色
const deleteAuth = (row) => {
  ElMessageBox.confirm('此操作将永久删除该角色, 是否继续?', '提示', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning'
  })
    .then(async () => {
      const res = await deleteAuthority({ authorityId: row.authorityId })
      if (res.code === 0) {
        ElMessage({
          type: 'success',
          message: '删除成功!'
        })
        if (tableData.value.length === 1 && page.value > 1) {
          page.value--
        }
        getTableData()
      }
    })
    .catch(() => {
      ElMessage({
        type: 'info',
        message: '已取消删除'
      })
    })
}

// 初始化表单
const authorityForm = ref(null)
const initForm = () => {
  if (authorityForm.value) {
    authorityForm.value.resetFields()
  }
  form.value = {
    authorityId: '',
    authorityName: '',
    authoritySys: 0,
    authorityDescribe: '',
    parentId: '0'
  }
}
// 关闭窗口
const closeDialog = () => {
  initForm()
  dialogFormVisible.value = false
  apiDialogFlag.value = false
}
// 确定弹窗
const enterDialog = async() => {
  form.value.authorityId = Number(form.value.authorityId)
  if (form.value.authorityId === '0') {
    ElMessage({
      type: 'error',
      message: '角色id不能为0'
    })
    return false
  }
  authorityForm.value.validate(async valid => {
    if (valid) {
      switch (dialogType.value) {
        case 'add':
          {
            const res = await createAuthority(form.value)
            if (res.code === 0) {
              ElMessage({
                type: 'success',
                message: '添加成功!'
              })
              getTableData()
              closeDialog()
            }
          }
          break
        case 'edit':
          {
            const res = await updateAuthority(form.value)
            if (res.code === 0) {
              ElMessage({
                type: 'success',
                message: '编辑成功!'
              })
              getTableData()
              closeDialog()
            }
          }
          break
        case 'copy': {
          const data = {
            authority: {
              authorityId: 'string',
              authorityName: 'string',
              datauthorityId: [],
              parentId: 'string'
            },
            oldAuthorityId: 0
          }
          data.authority.authorityId = form.value.authorityId
          data.authority.authorityName = form.value.authorityName
          data.authority.authoritySys = form.value.authoritySys
          data.authority.authorityDescribe = form.value.authorityDescribe
          data.authority.parentId = form.value.parentId
          data.authority.dataAuthorityId = copyForm.value.dataAuthorityId
          data.oldAuthorityId = copyForm.value.authorityId
          const res = await copyAuthority(data)
          if (res.code === 0) {
            ElMessage({
              type: 'success',
              message: '复制成功！'
            })
            getTableData()
          }
        }
      }
      initForm()
      dialogFormVisible.value = false
    }
  })
}
const setOptions = () => {
  AuthorityOption.value = [
    {
      authorityId: '0',
      authorityName: '根角色'
    }
  ]
  setAuthorityOptions(tableData.value, AuthorityOption.value, false)
}
const setAuthorityOptions = (AuthorityData, optionsData, disabled) => {
  form.value.authorityId = String(form.value.authorityId)
  console.log(AuthorityData)
  AuthorityData &&
    AuthorityData.forEach(item => {
      if (item.children && item.children.length) {
        const option = {
          authorityId: item.authorityId,
          authorityName: item.authorityName,
          disabled: disabled || item.authorityId === form.value.authorityId,
          children: []
        }
        setAuthorityOptions(
          item.children,
          option.children,
          disabled || item.authorityId === form.value.authorityId
        )
        optionsData.push(option)
      } else {
        const option = {
          authorityId: item.authorityId,
          authorityName: item.authorityName,
          disabled: disabled || item.authorityId === form.value.authorityId
        }
        optionsData.push(option)
      }
    })
}
// 增加角色
const addAuthority = (parentId) => {
  initForm()
  dialogTitle.value = '新增角色'
  dialogType.value = 'add'
  form.value.parentId = parentId
  setOptions()
  dialogFormVisible.value = true
}
// 编辑角色
const editAuthority = (row) => {
  setOptions()
  dialogTitle.value = '编辑角色'
  dialogType.value = 'edit'
  for (const key in form.value) {
    form.value[key] = row[key]
  }
  setOptions()
  dialogFormVisible.value = true
}
</script>


<style lang="scss">
// .authority {
// .el-input-number {
//   margin-left: 15px;
//   span {
//     display: none;
//   }
// }
// }
.role-box {
  .el-tabs__content {
    height: calc(100vh - 72px);
    overflow: auto;
  }
}
</style>
