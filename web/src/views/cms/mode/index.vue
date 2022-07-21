<template>
    <div>
        <warning-bar title="注：功能待完善" />
        <div class="gva-table-box">
            <div class="gva-btn-list">
                <el-button
                    type="primary"
                    @click="openDialog('add', 0)"
                    icon="plus"
                >添加根模型</el-button>
            </div>
            <el-table :data="tableData" border row-key="id" style="width: 100%">
                <el-table-column label="模型ID" min-width="80" prop="id" />
                <el-table-column label="模型名称" min-width="100" prop="name" />
                <el-table-column label="模型表名" min-width="160" prop="table" />
                <el-table-column label="模型路径" min-width="260" prop="component" />
                <el-table-column label="模型描述" min-width="300" prop="desc" />
                <el-table-column label="排序" min-width="80" prop="sort" />
                <el-table-column fixed="right" label="操作" width="230">
                    <template #default="scope">
                        <el-button
                            type="primary"
                            @click="openDialog('edit', scope.row.id, scope.row)"
                        >编辑</el-button>
                        <el-button
                            type="primary"
                            @click="openDialog('add', scope.row.id)"
                        >添加子模型</el-button>
                        <el-button type="primary" @click="deleteMode(scope.row.id)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <el-dialog v-model="dialogFormVisible" :before-close="closeDialog" :title="dialogTitle">
            <el-form ref="ModeForm" :model="form" :rules="rules">
                <el-form-item label="父级模型" prop="parentId" :label-width="formLabelWidth">
                    <el-cascader
                        v-model="form.parentId"
                        style="width:100%"
                        :disabled="dialogType == 'add'"
                        :options="modeOption"
                        :props="{ checkStrictly: true, label: 'name', value: 'id', disabled: 'disabled', emitPath: false }"
                        :show-all-levels="false"
                        filterable
                    />
                </el-form-item>
                <el-form-item label="模型名称" prop="name" :label-width="formLabelWidth">
                    <el-input v-model="form.name" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="模型表名" prop="table" :label-width="formLabelWidth">
                    <el-input v-model="form.table"></el-input>
                </el-form-item>
                <el-form-item label="模型路径" prop="component" :label-width="formLabelWidth">
                    <el-input v-model="form.component" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="模板描述" prop="desc" :label-width="formLabelWidth">
                    <el-input v-model="form.desc" type="textarea" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="排序" prop="sort" :label-width="formLabelWidth">
                    <el-input v-model.number="form.sort" autocomplete="off"></el-input>
                </el-form-item>
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
    name: 'Mode',
}
</script>

<script setup>
import { reactive, ref } from 'vue';
import warningBar from '@/components/warningBar/warningBar.vue';
import { ElMessage, ElMessageBox } from 'element-plus'
import { addMode, getModeList, getModeById, editModeById, deleteModeById } from '@/api/mode'
const formLabelWidth = '80px'
const dialogTitle = ref('添加模型')
const dialogFormVisible = ref(false)

const type = ref('')
const form = ref({
    id: '',
    parentId: '0',
    name: '',
    table: '',
    desc: '',
    sort: 0,
    component: ''
})
const modeOption = ref([
    {
        name: '根模型',
        id: '0',
    }
])
const dialogType = ref('')
const tableData = ref([])
// const optionsData = ref([])
// 打开表单窗口
const openDialog = async (key, id, row) => {
    switch (key) {
        case 'add':
            dialogTitle.value = '添加模型'
            dialogType.value = 'add'
            form.value.parentId = String(id)
            setOptions()
            break
        case 'edit':
            dialogTitle.value = '编辑模型'
            dialogType.value = 'edit'
            initForm()
            for (const key in form.value) {
                form.value[key] = row[key]
            }
            form.value['id'] = String(row['id'])
            console.log(form.value)
            setOptions()
            break
        default:
            break
    }
    type.value = key
    dialogFormVisible.value = true
}
const setOptions = () => {
    modeOption.value = [{
        id: '0',
        name: '根角色'
    }]
    setModeOptions(tableData.value, modeOption.value, false)
}
const setModeOptions = (modeData, optionsData, disabled) => {
    modeData &&
        modeData.forEach(item => {
            if (item.children && item.children.length) {
                const option = {
                    id: String(item.id),
                    name: item.name,
                    disabled: disabled || item.id === form.value.id,
                    children: []
                }
                setModeOptions(
                    item.children,
                    option.children,
                    disabled || item.id === form.value.id
                )
                optionsData.push(option)
            } else {
                const option = {
                    id: String(item.id),
                    name: item.name,
                    disabled: disabled || item.id === form.value.id
                }
                optionsData.push(option)
            }
        })
}

// 弹窗相关
const ModeForm = ref(null)
const initForm = () => {
    if (ModeForm.value) {
        ModeForm.value.resetFields()
    }
    form.value = {
        parentId: '0',
        id: '',
        name: '',
        table: '',
        desc: '',
        sort: 0,
        component: ''
    }
}
const rules = ref({
    name: [
        { required: true, message: '请输入模型名称', trigger: 'blur' },
        { min: 2, message: '最低2位字符', trigger: 'blur' }
    ],
    component: [
        { required: false, message: '请输入模型名称', trigger: 'blur' },
    ],
    sort: [
        { type: 'number', message: '必须为数字' }
    ]
})

const enterDialog = async () => {
    ModeForm.value.validate(async valid => {
        if (valid) {
            switch (type.value) {
                case 'add':
                    {
                        console.log(form.value)
                        const res = await addMode(form.value)
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
                        const r = await editModeById(form.value)
                        console.log(form.value)
                        closeDialog()
                        await getTableData()
                    }
            }
        }
    }
    )
}
// 查询
const getTableData = async () => {
    const table = await getModeList()
    if (table.code === 0) {
        tableData.value = table.data.list
    }
}
getTableData()

const closeDialog = () => {
    initForm()
    dialogFormVisible.value = false
}

// 删除
const deleteMode = async (id) => {
    const res = await deleteModeById(id)
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

// const form = reactive({
//     name: '',
//     sort: 0,
// })

</script>