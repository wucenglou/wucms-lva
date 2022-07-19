<template>
  <div>
    <warningBar title="注：功能待完善" />
    <div class="gva-search-box">
      <el-form :inline="true">
        <el-form-item label="选择模型">
          <el-cascader
            v-model="searchInfo.modeId"
            :options="tableDataMode"
            :props="{ checkStrictly: true, label: 'name', value: 'id', disabled: 'disabled', emitPath: false }"
            :show-all-levels="false"
            @change="BlurChange"
            @visible-change="Blur"
            filterable
          />
        </el-form-item>
        <el-form-item label="栏目分类">
          <el-cascader
            v-model="searchInfo.catIds"
            :options="tableDataCat"
            placeholder="栏目分类"
            @change="BlurChange"
            @visible-change="Blur"
            :props="{
              multiple: true,
              checkStrictly: true, label: 'metaTitle', value: 'id', disabled: 'disabled', emitPath: false
            }"
            collapse-tags
            clearable
          />
        </el-form-item>
        <el-form-item label="关键词搜索">
          <el-input placeholder="搜索" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" icon="search">查询</el-button>
          <el-button icon="refresh">重置</el-button>
        </el-form-item>
      </el-form>
    </div>
    <div class="gva-table-box">
      <div class="gva-btn-list">
        <el-button @click="onSubmit('add', 0)" type="primary">添加内容</el-button>
        <!-- <el-button @click="onSubmit('deleteids', 0)" type="danger">批量删除</el-button> -->
        <el-select
          v-model="statusType"
          placeholder="Select"
          style="width:10rem;margin-right: 1rem;margin-left: 1rem;"
        >
          <el-option
            v-for="item in options"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          ></el-option>
        </el-select>
        <el-button @click="onSubmit('batch', 0)" type="primary">批量操作</el-button>
      </div>
      <el-table
        :data="tableDataPost"
        ref="multipleTableRef"
        @selection-change="handleSelectionChange"
        border
        row-key="id"
        style="width: 100%"
      >
        <el-table-column type="selection" width="40" />
        <el-table-column label="id" min-width="60" prop="id" />
        <el-table-column label="类型" min-width="80" prop="modeName" />
        <el-table-column label="分类" min-width="80" prop="catName" />
        <el-table-column label="文章标题" min-width="100" prop="title" />
        <el-table-column label="作者" min-width="100" prop="authorName" />
        <el-table-column label="状态" min-width="80" prop="status">
          <template #default="scope">
            <span v-if="scope.row.status == 1">
              <el-button type="success">{{ scope.row.statusName }}</el-button>
            </span>
            <span v-else-if="scope.row.status == -1">
              <el-button type="danger" plain>{{ scope.row.statusName }}</el-button>
            </span>
            <span v-else-if="scope.row.status == 0">
              <el-button type="danger">{{ scope.row.statusName }}</el-button>
            </span>
            <span v-else>
              <el-button type="info" plain>{{ scope.row.statusName }}</el-button>
            </span>
          </template>
        </el-table-column>
        <!-- <el-table-column label="栏目名称" min-width="100" prop="metaTitle" />
        <el-table-column label="栏目模型" min-width="100">
          <template #default="scope">
            <span>{{ modeList[scope.row.modeId] }}</span>
          </template>
        </el-table-column>
        <el-table-column label="排序" min-width="80" prop="sort" />

        <el-table-column label="隐藏" min-width="100" prop="hidden">
          <template #default="scope">
            <span>{{ scope.row.hidden ? "隐藏" : "显示" }}</span>
          </template>
        </el-table-column>-->

        <el-table-column fixed="right" label="操作" width="160">
          <template #default="scope">
            <el-button
              size="small"
              type="text"
              @click="onSubmit('edit', scope.row.id, scope.row, 'check')"
            >预览</el-button>
            <el-button type="text" @click="onSubmit('edit', scope.row.id, scope.row)">编辑</el-button>
            <!-- <el-button type="text" @click="onSubmit('add', scope.row.id)">添加子栏目</el-button> -->
            <el-button
              size="small"
              type="text"
              @click="onSubmit('delete', scope.row.id, scope.row)"
            >删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="gva-pagination">
        <el-pagination
          small
          :current-page="page"
          :page-size="pageSize"
          :page-sizes="[10, 20, 30, 40]"
          background
          layout="total, sizes, prev, pager, next, jumper"
          :total="total"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        ></el-pagination>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'content',
}
</script>

<script setup>
import { defineComponent, ref } from 'vue'
import { getModeList } from '@/api/mode'
import { getPosts, deletePost, optionsPost } from '@/api/post'
import { useRouter, useRoute } from 'vue-router'
import { getCatList } from '@/api/cat'
import warningBar from '@/components/warningBar/warningBar.vue';
import { ElMessage, ElMessageBox } from 'element-plus'

const search = ref({
  id: '',
  modeId: 0,
  catId: [],
  keyword: '',
  status: '',
})

const page = ref(1)
const total = ref(0)
const pageSize = ref(10)
const searchInfo = ref({})

// 批量操作
const options = [
  {
    value: '1',
    label: '审核通过',
  },
  {
    value: '-1',
    label: '审核未通过',
  },
  {
    value: '0',
    label: '待审核',
  },
  {
    value: '-2',
    label: '设为草稿',
  },
  {
    value: '2',
    label: '过期',
  },
  {
    value: 'Delete',
    label: '删除',
  }]
const statusType = ref('')
// 查询
const tableDataMode = ref([])
const tableDataCat = ref([])
const modeList = ref([])
const tableDataPost = ref([])
const getTableData = async () => {
  const tableMode = await getModeList()
  if (tableMode.code === 0) {
    tableDataMode.value = tableMode.data.list
    searchInfo.value.modeId = tableMode.data.list[0]['id']
    modeArr(tableDataMode.value, modeList.value)
  }
  const tableCat = await getCatList()
  if (tableCat.code === 0) {
    tableDataCat.value = tableCat.data.list
  }
  searchInfo.value.status = 0
  const tablePost = await getPosts({page: page.value, pageSize: pageSize.value, ...searchInfo.value})
  if (tablePost.code === 0) {
    tableDataPost.value = tablePost.data.list
    page.value = tablePost.data.page
    pageSize.value = tablePost.data.pageSize
    total.value = tablePost.data.total
  }
}
getTableData()
const refreshTableData = async () => {
  const tablePost = await getPosts({ page: page.value, pageSize: pageSize.value, ...searchInfo.value })
  if (tablePost.code === 0) {
    tableDataPost.value = tablePost.data.list
    page.value = tablePost.data.page
    pageSize.value = tablePost.data.pageSize
    total.value = tablePost.data.total
  }
}

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

// 点击按钮
const router = useRouter()
const onSubmit = async (key, id, row, type) => {
  switch (key) {
    case 'add':
      console.log('250')
      router.push({ name: 'edit' })
      break
    case 'edit':
      console.log('编辑')
      console.log(row)
      router.push({ name: 'edit', params: { post_id: id, mode_id: row.modeId, type: type } })
      break
    case 'delete':
      deleteInfo.value.modeId = row.modeId
      deleteInfo.value.ids.push(row.id)
      const res = await deletePost(deleteInfo.value)
      if (res.code === 0) {
        ElMessage({
          type: 'success',
          message: '删除成功',
          showClose: true
        })
        await getTableData()
      }
      break
    case 'batch':
      if (deleteInfo.value.ids != '') {
        deleteInfo.value.type = statusType.value
        const batch = await optionsPost(deleteInfo.value)
        if (batch.code === 0) {
          ElMessage({
            type: 'success',
            message: batch.msg,
            showClose: true
          })
          const tablePost = await getPosts(search.value)
          setTablePost(tablePost)
        }
      }
      break
    case 'deleteids':
      const res_ids = await deletePost(deleteInfo.value)
      if (res_ids.code === 0) {
        ElMessage({
          type: 'success',
          message: '删除成功',
          showClose: true
        })
        await getTableData()
      }
      break
    default:
      break
  }
}
const deleteInfo = ref({
  ids: [],
  modeId: '',
})
const handleSelectionChange = (val) => {
  deleteInfo.value.ids = []
  val.forEach(item => {
    deleteInfo.value.ids.push(item.id)
    deleteInfo.value.modeId = search.value.modeId
  });
}

const handleSizeChange = (val) => {
  pageSize.value = val
  refreshTableData()
}
const handleCurrentChange = async (val) => {
  page.value = val
  refreshTableData()
}

const haveChange = ref(false)
const BlurChange = async (v) => {
  haveChange.value = true
}

const Blur = async (v) => {
  if (!v && haveChange.value) {
    haveChange.value = false
    console.log(searchInfo.value)
    refreshTableData()
  }
}

</script>