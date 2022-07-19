import service from '@/utils/request'

export const postPost = (data) => {
    return service({
        url: '/post',
        method: 'post',
        data: data
    })
}

export const getPost = (data) => {
    console.log(data)
    return service({
        url: '/getpost',
        method: 'post',
        data: data
    })
}

export const getPosts = (data) => {
    return service({
        url: '/posts',
        method: 'post',
        data: data
    })
}

export const deletePost = (data) => {
    return service({
        url: '/post',
        method: 'delete',
        data: data
    })
}

export const optionsPost = (data) => {
    return service({
        url: '/optionspost',
        method: 'post',
        data: data
    })
}

export const getComments = (data) => {
    return service({
        url: '/comment',
        method: 'post',
        data: data
    })
}
