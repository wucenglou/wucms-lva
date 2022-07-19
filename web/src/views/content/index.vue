<template>
  <div>
    <div class="gva-table-box">
      <div class="gva-btn-list">
         <el-button size="mini" type="primary" icon="plus" >添加栏目</el-button>
         <el-button size="mini" type="primary" icon="plus" >添加单页</el-button>
         <el-button size="mini" type="primary" icon="plus" >添加外部链接</el-button>
         <el-button size="mini" type="primary" icon="plus" >批量添加</el-button>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

const value = ref('')

const options = [
  {
    value: 'Option1',
    label: 'Option1',
  },
  {
    value: 'Option2',
    label: 'Option2',
  },
  {
    value: 'Option3',
    label: 'Option3',
  },
  {
    value: 'Option4',
    label: 'Option4',
  },
  {
    value: 'Option5',
    label: 'Option5',
  },
]
</script>