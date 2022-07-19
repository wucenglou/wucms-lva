import service from '@/utils/request'

export const addMode = (data) => {
    return service({
        url: '/mode',
        method: 'post',
        data: data
    })
}

export const deleteModeById = (data) => {
    return service({
        url: '/modeById',
        method: 'DELETE',
        data: data
    })
}

export const editModeById = (data) => {
    return service({
        url: '/modeById',
        method: 'put',
        data: data
    })
}

export const getModeById = (data) => {
    return service({
        url: '/modeById/' + data,
        method: 'get',
        // data: data
    })
}

// export const getModeById = (data) => {
//     return service({
//         url: '/mode',
//         method: 'get',
//         data: data
//     })
// }

export const getModeList = (data) => {
    return service({
        url: '/modeList',
        method: 'get',
        data: data
    })
}




