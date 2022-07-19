import { createApp } from 'vue'
import 'element-plus/dist/index.css'
import './style/element_visiable.scss'
import ElementPlus from 'element-plus'
import zhCn from 'element-plus/es/locale/lang/zh-cn'

import run from '@/core/wucms-vue.js'
import router from '@/router/index'
import '@/permission'

import { store } from '@/store'
import App from './App.vue'


createApp(App)
    .use(run)
    .use(store)
    .use(router)
    .use(ElementPlus, { locale: zhCn, size: 'small' }).mount('#app')
