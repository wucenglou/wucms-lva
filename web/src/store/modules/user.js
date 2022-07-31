import { login, getUserInfo, setUserInfo } from '@/api/user'
import { jsonInBlacklist, logout } from '@/api/jwt'
import router from '@/router/index'
import { ElLoading, ElMessage } from 'element-plus'
import { defineStore } from 'pinia'
import { computed, ref, watch } from 'vue'
import { useRouterStore } from './router'

export const useUserStore = defineStore('user', () => {
    const loadingInstance = ref(null)
    const userInfo = ref({
        uuid: '',
        nickName: '',
        avatarUrl: '',
        authority: {},
        sideMode: 'dark',
        activeColor: '#4D70FF',
        baseColor: '#fff'
    })
    const token = ref(window.localStorage.getItem('token') || '')
    const setUserInfo = (val) => {
        userInfo.value = val
    }

    const setToken = (val) => {
        token.value = val
    }
    const NeedInit = () => {
        token.value = ''
        window.localStorage.removeItem('token')
        localStorage.clear()
        router.push({ name: 'Init', replace: true })
    }
    const ResetUserInfo = (value = {}) => {
        userInfo.value = {
            ...userInfo.value,
            ...value
        }
    }
    // 获取用户信息
    const GetUserInfo = async() => {
        const res = await getUserInfo()
        if(res.code === 0) {
            setUserInfo(res.data.userInfo)
        }
        return res
    }
    // 登录
    const LoginIn = async(loginInfo) => {
        loadingInstance.value = ElLoading.service({
            fullscreen: true,
            text: '登录中，请稍后...',
        })
        try {
            const res = await login(loginInfo)
            if(res.code === 0) {
                setUserInfo(res.data.user)
                setToken(res.data.token)
                const routerStore = useRouterStore()
                await routerStore.SetAsyncRouter()
                const asyncRouters = routerStore.asyncRouters
                asyncRouters.forEach(asyncRouter => {
                    router.addRoute(asyncRouter)
                });
                router.push({ name: "dashboard" })
                // router.push({ name: userInfo.value.authority.defaultRouter})
                loadingInstance.value.close()
                return true
            }
        } catch (e) {
            loadingInstance.value.close()
        }
        loadingInstance.value.close()
    }
    // 登出
    const LoginOut = async() => {
        const res = await logout()
        if(res.code === 0) {
            token.value = ''
            sessionStorage.clear()
            localStorage.clear()
            router.push({ name: 'Login', replace: true })
            window.location.reload()
        }
    }
    // 清理数据
    const ClearStorage = async() => {
        token.value = ''
        sessionStorage.clear()
        localStorage.clear()
    }
    const changeSideMode = async(data) => {
        const res = await setUserInfo({ sideMode: data, ID: state.userInfo.ID })
        if (res.code === 0) {
            userInfo.value.sideMode = data
            ElMessage({
                type: 'success',
                message: '设置成功'
            })
        }
    }

    const mode = computed(() => userInfo.value.sideMode)
    const sideMode = computed(() => {
        if (userInfo.value.sideMode === 'dark') {
            return '#2f3447'
        } else if (userInfo.value.sideMode === 'light') {
            return '#fff'
        } else {
            return userInfo.value.sideMode
        }
    })
    const baseColor = computed(() => {
        if (userInfo.value.sideMode === 'dark') {
            return '#fff'
        } else if (userInfo.value.sideMode === 'light') {
            return '#191a23'
        } else {
            return userInfo.value.sideMode
        }
    })
    const activeColor = computed(() => {
        if (userInfo.value.sideMode === 'dark' || userInfo.value.sideMode === 'light') {
            return '#409eff'
        }
        return userInfo.value.sideMode
    })

    watch(() => token.value, () => {
        window.localStorage.setItem('token', token.value)
    })

    return {
        userInfo,
        token,
        NeedInit,
        ResetUserInfo,
        GetUserInfo,
        LoginIn,
        LoginOut,
        changeSideMode,
        mode,
        sideMode,
        setToken,
        baseColor,
        activeColor,
        loadingInstance,
        ClearStorage  
    }
})