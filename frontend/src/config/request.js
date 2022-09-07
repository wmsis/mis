export default {
    auth: {
        signin: '/auth/login',
        logout: '/auth/logout',
        refresh: '/auth/refresh'
    },
    member: {
        page: '/member/lists',
        detail: '/member/{id}/info',
        takemoney: '/member/takemoney/lists',
        takemoneyCheck: '/member/takemoney/check'
    },
    wechatMenu: {
        list: '/wechat/menus',
        children: '/wechat/menuchildren',
        insert: '/wechat/insertmenu',
        update: '/wechat/updatemenu',
        delete: '/wechat/deletemenu',
        publish: '/wechat/publishmenu'
    },
    keyword: {
        page: '/wechat/keywords',
        list: '/wechat/keylists',
        store: '/wechat/storeautoreply',
        delete: '/wechat/deleteautoreply'
    },
    autoreply: {
        detail: '/wechat/autoreply',
        store: '/wechat/storeautoreply'
    },
    picTxt: {
        list: '/wechat/pictxtlistall',
        page: '/wechat/pictxtlist',
        store: '/wechat/storepictxt',
        delete: '/wechat/deletepictxt',
        all: '/wechat/pictxtlistall',
        sendPicText: '/wechat/storememberpictxt',
        queue: '/wechat/pictxtqueue',
        delQueue: '/wechat/deletepictxtqueue',
        batchDelQueue: '/wechat/pictxtqueue/batchdelete',
        qrcode: '/wechat/qrcode'
    },
    material: {
        list: '/wechat/pictxt/{id}/materials',
        store: '/wechat/storematerial',
        delete: '/wechat/deletematerial',
        upload: '/wechat/upload',
        detail: '/wechat/material/{id}'
    },
    users: {
        page: '/users',
        store: '/users/store',
        delete: '/users/delete',
        changePassword: '/users/chgpwd',
        resetPassword: '/users/resetpwd',
        roleList: '/users/{id}/role',
        assignRoles: '/users/{id}/role',
        orgnizationList: '/users/{id}/orgnization',
        assignOrgnizations: '/users/{id}/orgnization',
        bindMember: '/users/bind-member'
    },
    roles: {
        page: '/roles',
        list: '/roles/lists',
        store: "/roles/store",
        delete: '/roles/delete',
        permissionList: '/roles/{id}/permission',
        savePermission: '/roles/{id}/permission',
        apiList: '/roles/{role}/api',
        saveApi: '/roles/{role}/api'
    },
    permission: {
        tree: '/permissions/tree',
        insert: '/permissions/insert',
        update: '/permissions/update',
        delete: '/permissions/delete'
    },
    setting: {
        limitload:'/historian-tag/index',
        alltag:'/historian-tag/all',
        limitupdate:'/historian-tag/update/{id}',
    },
    historianData: {
        currentdata: '/historian-data/current-data',
        sampleddata: '/historian-data/sampled-data',
        rawdata: '/historian-data/raw-data',
        watchdata: '/historian-data/watch-data'
    },
    orgnization: {
        tree: '/orgnizations/tree',
        store: "/orgnizations/store",
        delete: '/orgnizations/delete',
        roleList: '/orgnizations/{id}/role',
        assignRoles: '/orgnizations/{id}/role'
    },
    api: {
        tree: '/api/tree',
        store: "/api/store",
        delete: '/api/delete',
    },
}
