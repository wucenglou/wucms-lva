import router from '@/router'
import { useUserStore } from '@/store/modules/user'
import { useRouterStore } from '@/store/modules/router'
import getPageTitle from '@/utils/page'

let asyncRouterFlag = 0
const whiteList = ['Login', 'Init']

const getRouter = async (userStore) => {
    const routerStore = useRouterStore()
    await routerStore.SetAsyncRouter()
    await userStore.GetUserInfo()
    const asyncRouters = routerStore.asyncRouters
    asyncRouters.forEach(asyncRouter => {
        router.addRoute(asyncRouter)
    });
    // await store.dispatch('router/SetAsyncRouter')
    // await store.dispatch('user/GetUserInfo')
    // const asyncRouters = store.getters['router/asyncRouters']
    // asyncRouters.forEach(asyncRouter => {
    //     router.addRoute(asyncRouter)
    // })
}

async function handleKeepAlive(to) {
    if (to.matched.some(item => item.meta.keepAlive)) {
        if (to.matched && to.matched.length > 2) {
            for (let i = 1; i < to.matched.length; i++) {
                const element = to.matched[i - 1]
                if (element.name === 'layout') {
                    to.matched.splice(i, 1)
                    await handleKeepAlive(to)
                }
                // 如果没有按需加载完成则等待加载
                if (typeof element.components.default === 'function') {
                    await element.components.default()
                    await handleKeepAlive(to)
                }
            }
        }
    }
}

router.beforeEach(async (to, from) => {
    const userStore = useUserStore()
    to.meta.matched = [...to.matched]
    handleKeepAlive(to)
    const token = userStore.token
    // const token = store.getters['user/token']
    // 在白名单中的判断情况
    document.title = getPageTitle(to.meta.title, to)
    if (whiteList.indexOf(to.name) > -1) {
        if (token) {
            if (!asyncRouterFlag) {
                asyncRouterFlag++
                await getRouter(userStore)
            }
            // token 可以解析但是却是不存在的用户 id 或角色 id 会导致无限调用
            if (userStore.userInfo?.authority?.defaultRouter != null) {
                return { name: userStore.userInfo.authority.defaultRouter }
            } else {
                // 强制退出账号
                userStore.ClearStorage()
                return {
                    name: 'Login',
                    query: {
                        redirect: document.location.hash
                    }
                }
            }
        } else {
            return true
        }
    } else {

        // 不在白名单中并且已经登陆的时候
        if (token) {
            // 添加flag防止多次获取动态路由和栈溢出
            if (!asyncRouterFlag) {
                asyncRouterFlag++
                await getRouter(userStore)
                if (userStore.token) {
                    return { ...to, replace: true }
                } else {
                    return {
                        name: 'Login',
                        query: { redirect: to.href }
                    }
                }
            } else {
                if (to.matched.length) {
                    return true
                } else {
                    return { path: '/layout/404' }
                }
            }
        }
        // 不在白名单中并且未登陆的时候
        if (!token) {
            return {
                name: 'Login',
                query: {
                    redirect: document.location.hash
                }
            }
            // next({
            //     name: 'Login',
            //     query: {
            //         redirect: document.location.hash
            //     }
            // })
        }
    }
})
