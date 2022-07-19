<template>
    <div>
        <div class="gva-search-box">
            <el-form :inline="true">
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
            <!-- <div class="gva-btn-list">
                <el-button @click="onSubmit('add', 0)" type="primary">添加内容</el-button>
                <el-select
                    v-model="statusType"
                    placeholder="Select"
                    style="width:10rem;margin-right: 1rem;margin-left: 1rem;"
                >
                    <el-option :key="item.value" :label="item.label" :value="item.value"></el-option>
                </el-select>
                <el-button @click="onSubmit('batch', 0)" type="primary">批量操作</el-button>
            </div> -->

            <el-table :data="tableData" border>
                <el-table-column type="selection" width="55" />
                <el-table-column align="left" label="编号id" min-width="60" prop="id" />
                <el-table-column align="left" label="用户名" min-width="100" prop="username" />
                <el-table-column align="left" label="登录地址" min-width="180" prop="address" />
                <el-table-column align="left" label="登录ip地址" min-width="130" prop="ip" />
                <el-table-column align="left" label="浏览器" min-width="180" prop="browserInfo" />
                <el-table-column align="left" label="操作系统" min-width="120" prop="platformInfo" />
                <el-table-column align="left" label="设备" min-width="130" prop="deviceInfo" />
                <el-table-column align="left" label="登录状态" min-width="90" prop="status">
                    <template #default="scope">
                        <span v-if="scope.row.status == 1">
                            <el-button type="success">成功</el-button>
                        </span>
                        <span v-else>
                            <el-button type="warning">失败</el-button>
                        </span>
                    </template>
                </el-table-column>
                <el-table-column align="left" label="操作信息" min-width="160" prop="msg" />
                <el-table-column align="left" label="登录时间" min-width="160" prop="createdAt" />
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
    </div>
</template>

<script setup>

import { getloginLog } from '@/api/user'
import { ref } from 'vue'

const page = ref(1)
const total = ref(0)
const pageSize = ref(10)
const tableData = ref([])
const searchInfo = ref({})

// 查询
const getTableData = async () => {
    const table = await getloginLog({ page: page.value, pageSize: pageSize.value, ...searchInfo.value })
    console.log(table)
    if (table.code === 0) {
        tableData.value = table.data.list
        total.value = table.data.total
        page.value = table.data.page
        pageSize.value = table.data.pageSize
    }
}
getTableData()


// 分页
const handleSizeChange = (val) => {
    pageSize.value = val
    getTableData()
}
const handleCurrentChange = (val) => {
    page.value = val
    getTableData()
}

</script>