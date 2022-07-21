<template>
  <div>
    <div class="gva-table-box">
      <div class="gva-btn-list">
        <el-button type="primary" icon="plus" @click="openDialog('add', 0)">新增根栏目</el-button>
        <el-button type="primary" icon="plus" @click="openDialog('add', 0)">添加单页</el-button>
        <el-button type="primary" icon="plus" @click="openDialog('add', 0)">批量添加</el-button>
      </div>

      <el-table :data="tableDataCat" default-expand-all border row-key="id" style="width: 100%">
        <el-table-column label="栏目id" min-width="80" prop="id" />
        <el-table-column label="栏目名称" min-width="100" prop="metaTitle" />
        <el-table-column label="栏目模型" min-width="100">
          <template #default="scope">
            <span>{{ modeList[scope.row.modeId] }}</span>
          </template>
        </el-table-column>
        <el-table-column label="URL" min-width="80" prop="name" />
        <el-table-column label="排序" min-width="80" prop="sort" />

        <el-table-column label="隐藏" min-width="100" prop="hidden">
          <template #default="scope">
            <span>{{ scope.row.hidden ? "隐藏" : "显示" }}</span>
          </template>
        </el-table-column>

        <el-table-column fixed="right" label="操作" width="230">
          <template #default="scope">
            <el-button type="primary" @click="openDialog('edit', scope.row.id, scope.row)">编辑</el-button>
            <el-button type="primary" @click="openDialog('add', scope.row.id)">添加子栏目</el-button>
            <el-button type="primary" @click="deleteCat(scope.row.id)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>

    <el-dialog v-model="dialogFormVisible" :before-close="closeDialog" :title="dialogTitle">
      <el-form ref="CatForm" :model="form" :rules="rules">
        <el-tabs style="height: 100%">
          <el-tab-pane label="基本选项">
            <warning-bar title="注：默认name和path相同" />

            <el-form-item label="选择模型" :label-width="formLabelWidth">
              <el-cascader v-model="form.modeId" :disabled="dialogType == 'edit'" :options="modeOption"
                :props="{ checkStrictly: true, label: 'name', value: 'id', disabled: 'disabled', emitPath: false }"
                :show-all-levels="false" filterable />
            </el-form-item>

            <el-form-item label="上级栏目" :label-width="formLabelWidth">
              <el-cascader v-model="form.parentId" :disabled="dialogType == 'add'" :options="catOption"
                :props="{ checkStrictly: true, label: 'metaTitle', value: 'id', disabled: 'disabled', emitPath: false }"
                :show-all-levels="false" filterable />
            </el-form-item>

            <el-form-item label="栏目名称" prop="metaTitle" :label-width="formLabelWidth">
              <el-input v-model="form.metaTitle"></el-input>
            </el-form-item>

            <el-form-item label="节点Name" prop="name" :label-width="formLabelWidth">
              <el-input v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item label="节点Path" prop="path" :label-width="formLabelWidth">
              <el-input v-model="form.path"></el-input>
            </el-form-item>

            <el-form-item label="栏目Icon" :label-width="formLabelWidth">
              <el-input v-model="form.metaIcon"></el-input>
            </el-form-item>
            <el-row>
              <el-col :xs="24" :sm="14">
                <el-form-item label="排序" :label-width="formLabelWidth" style="width: 18rem;">
                  <el-input v-model="form.sort"></el-input>
                </el-form-item>
              </el-col>

              <el-col :xs="24" :sm="8">
                <el-form-item label="隐藏" :label-width="formLabelWidth">
                  <el-switch v-model="form.hidden" :active-value="1" :inactive-value="0"></el-switch>
                </el-form-item>
              </el-col>
            </el-row>
          </el-tab-pane>

          <el-tab-pane label="SEO设置">
            <el-form-item label="栏目标题" :label-width="formLabelWidth">
              <el-input v-model="form.seoTitle" autocomplete="off"></el-input>
            </el-form-item>

            <el-form-item label="关键词" :label-width="formLabelWidth">
              <el-input v-model="form.seoKeywords" autocomplete="off"></el-input>
            </el-form-item>

            <el-form-item label="栏目描述" :label-width="formLabelWidth">
              <el-input v-model="form.seoDescription" type="textarea" autocomplete="off"></el-input>
            </el-form-item>
          </el-tab-pane>

          <el-tab-pane label="Banner管理">
            <el-upload ref="uploadRef" class="upload-demo" :data="filesinfo" :on-change="onUploadChange"
              :file-list="fileList" action="/api/uploadimgs" :auto-upload="false">
              <template #trigger>
                <el-button type="primary">选择图片</el-button>
              </template>
              <el-button class="ml-3" type="success" @click="submitUpload">开始上传</el-button>
              <template #tip>
                <div class="el-upload__tip">图片格式jpg/png 图片大小不超过 500kb</div>
              </template>
            </el-upload>
          </el-tab-pane>

          <el-tab-pane label="其他设置">
            <warning-bar title="注：vue应用配置" />
            <!-- <el-form-item label="后台菜单位置" label-width="8rem">
              <el-cascader
                v-model="form.menuId"
                :options="tableDataMenu"
                :props="{ checkStrictly: true, label: 'title', value: 'id', disabled: 'disabled', emitPath: false }"
                :show-all-levels="false"
                filterable
              />
            </el-form-item> -->

            <el-form-item label="KeepAlive" label-width="8rem">
              <el-switch v-model="form.metaKeepAlive" :active-value="0" :inactive-value="1"></el-switch>
            </el-form-item>
            <el-form-item label="defaultMenu" label-width="8rem">
              <el-switch v-model="form.metaDefaultMenu" :active-value="0" :inactive-value="1"></el-switch>
            </el-form-item>
            <el-form-item label="closeTab" label-width="8rem">
              <el-switch v-model="form.metaCloseTab" :active-value="0" :inactive-value="1"></el-switch>
            </el-form-item>
          </el-tab-pane>
        </el-tabs>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="closeDialog">取消</el-button>
          <el-button type="primary" @click="enterDialog">确认</el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'Cat',
}
</script>

<script setup>
import {
  updateBaseMenu,
  getMenuList,
  addBaseMenu,
  deleteBaseMenu,
  getBaseMenuById
} from '@/api/menu'
import { watch, ref, onMounted, onUnmounted } from 'vue'
import { getModeList } from '@/api/mode'
import { getCatList, postCatMenu, editCatMenu, deleteCatMenu } from '@/api/cat'
import warningBar from '@/components/warningBar/warningBar.vue';
import { ElMessage } from 'element-plus'

console.log('cat')

const dialogFormVisible = ref(false)
const formLabelWidth = '90px'
// 表单初始化 组件就是模型，模型就是组件
const form = ref({
  // id: '',
  parentId: '',
  modeId: 0,
  name: '',
  path: '',
  hidden: 0,
  sort: '0',

  seoTitle: '',
  seoKeywords: '',
  seoDescription: '',

  metaTitle: '',
  metaIcon: '',
  metaKeepAlive: '1',
  metaCloseTab: '0',
  metaDefaultMenu: '0',

  menuId: '0'
})
console.log('cat+++++++++++')
const initForm = () => {
  if (CatForm.value) {
    CatForm.value.resetFields()
  }
  modeOption.value = []
  catOption.value = [
    {
      id: '0',
      metaTitle: '根栏目',
    }
  ]
  form.value = {
    parentId: '',
    modeId: 0,
    name: '',
    path: '',
    hidden: 0,
    sort: '0',

    seoTitle: '',
    seoKeywords: '',
    seoDescription: '',

    metaTitle: '',
    metaIcon: '',
    metaKeepAlive: '1',
    metaCloseTab: '1',
    mataDefaultMenu: '0',

    menuId: '0'
  }
}
const dialogTitle = ref('新增栏目')
const CatForm = ref(null)
// 表单验证规则
const rules = ref({
  path: [
    { required: true, message: '请输入节点Path', trigger: 'blur' },
    { min: 2, message: '最低2位字符', trigger: 'blur' }
  ],
  name: [
    { required: true, message: '请输入节点Name', trigger: 'blur' },
    { min: 2, message: '最低2位字符', trigger: 'blur' }
  ],
  metaTitle: [
    { required: true, message: '请输入名称', trigger: 'blur' },
    { min: 2, message: '最低2位字符', trigger: 'blur' }
  ],
  sort: [
    { type: 'number', message: '必须为数字' }
  ]
})

const catOption = ref([
  {
    id: '0',
    name: '根模型',
    metaTitle: '根栏目',
  }
])
const modeOption = ref([

])
// 查询
const tableDataMode = ref([])
const tableDataCat = ref([])
const tableDataMenu = ref([])
const modeList = ref([])
const getTableData = async () => {
  const tableMenu = await getMenuList()
  if (tableMenu.code === 0) {
    console.log(tableMenu.data.list)
    tableDataMenu.value = tableMenu.data.list
    tableDataMenu.value.unshift(
      {
        id: '-1',
        title: '不调整',
      },
      {
        id: '0',
        title: '根栏目',
      }
    )
  }
  const tableMode = await getModeList()
  if (tableMode.code === 0) {
    tableDataMode.value = tableMode.data.list
    modeArr(tableDataMode.value, modeList.value)
  }
  const tableCat = await getCatList()
  if (tableCat.code === 0) {
    tableDataCat.value = tableCat.data.list
  }

}
getTableData()

// 拉平模型数组，让modeId和modeList得id相对应，并存储相对应的name
const modeArr = async (modeData, modeList) => {
  modeData &&
    modeData.forEach(item => {
      if (item.children && item.children.length) {
        modeList[item.id] = String(item.name)
        modeArr(item.children, modeList)
      } else {
        modeList[item.id] = String(item.name)
      }
    })
}

watch(() => form.value.name, () => {
  form.value.path = form.value.name
})

onMounted(() => {
  console.log('我创建了');
})
// 销毁实例变为onUnmounted，与vue2的destroy作用一致
onUnmounted(() => {
  console.log('我销毁了');
})
// 页面创建
// 打开弹窗表单
const dialogType = ref('')
const type = ref('')
const openDialog = async (key, id, row) => {
  switch (key) {
    case 'add':
      dialogTitle.value = '添加栏目'
      dialogType.value = 'add'
      form.value.parentId = String(id)
      console.log(form.value)
      setModeCatOptions()
      break
    case 'edit':
      dialogTitle.value = '编辑栏目'
      dialogType.value = 'edit'
      for (const key in form.value) {
        form.value[key] = row[key]
      }
      form.value['id'] = String(row['id'])
      setModeCatOptions()
      break
    default:
      break
  }
  type.value = key
  dialogFormVisible.value = true
}
// 确认提交表单
const enterDialog = async () => {
  CatForm.value.validate(async valid => {
    if (valid) {
      switch (type.value) {
        case 'add':
          {

            const res = await postCatMenu(form.value)
            if (res.code === 0) {
              ElMessage({
                type: 'success',
                message: '添加成功',
                showClose: true
              })
              await getTableData()
              closeDialog()
            }
          }
          break
        case 'edit':
          {
            console.log(form.value)
            const res = await editCatMenu(form.value)
            if (res.code === 0) {
              ElMessage({
                type: 'success',
                message: '22222'.res.msg,
                showClose: true
              })
              await getTableData()
              closeDialog()
            }
          }
      }
    }
  }
  )
}
const setModeCatOptions = () => {
  // modeOption.value = [{
  //   id: '0',
  //   name: '根角色222'
  // }]
  setOptions(tableDataMode.value, modeOption.value, false)
  setOptions(tableDataCat.value, catOption.value, false)
  console.log('----------')
  console.log(modeOption.value)
}
const setOptions = (modeData, optionsData, disabled) => {
  modeData &&
    modeData.forEach(item => {
      if (item.children && item.children.length) {
        const option = {
          id: String(item.id),
          name: item.name,
          metaTitle: item.metaTitle ? item.metaTitle : '',
          disabled: disabled || item.id === form.value.id,
          children: []
        }
        setOptions(
          item.children,
          option.children,
          disabled || item.id === form.value.id
        )
        optionsData.push(option)
      } else {
        const option = {
          id: String(item.id),
          name: item.name,
          metaTitle: item.metaTitle ? item.metaTitle : '',
          disabled: disabled || item.id === form.value.id
        }
        optionsData.push(option)
      }
    })
}
// 取消提交表单
const closeDialog = () => {
  initForm()
  dialogFormVisible.value = false
}

// 删除
const deleteCat = async (id) => {
  const res = await deleteCatMenu(id)
  if (res.code === 0) {
    ElMessage({
      type: 'success',
      message: '删除成功',
      showClose: true
    })
    await getTableData()
    closeDialog()
  }
}

// 图片上传
const fileList = ref([])
const filesinfo = ref({
  id: '',
  name: '',
  title: '',
  desc: ''

})
const uploadRef = ref({
})
const submitUpload = (row) => {
  filesinfo.value.id = form.value.id
  uploadRef.value.submit()
}

const onUploadChange = (file, list) => {
  console.log(file)
  console.log(list)
}
</script>