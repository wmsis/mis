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
    admins: {
        page: '/admins',
        store: '/admins/store',
        delete: '/admins/delete',
        changePassword: '/admins/chgpwd',
        resetPassword: '/admins/resetpwd',
        roleList: '/admins/{id}/role',
        assignRoles: '/admins/{id}/role',
        tagList: '/admins/{id}/tag',
        assignTags: '/admins/{id}/tag',
        bindMember: '/admins/bind-member'
    },
    roles: {
        page: '/roles',
        list: '/roles/lists',
        store: "/roles/store",
        delete: '/roles/delete',
        permissionList: '/roles/{id}/permission',
        savePermission: '/roles/{id}/permission'
    },
    permission: {
        list: '/permissions',
        all: '/permissions/all',
        children: '/permissions/children',
        insert: '/permissions/insert',
        update: '/permissions/update',
        delete: '/permissions/delete'
    },
    setting: {
        limitload:'/historian-tag/index',
        alltag:'/historian-tag/all',
        limitupdate:'/historian-tag/update/{id}',
        tagbindmodule:'/historian-tag/bind-module',
        tagrememberindex:'/historian-tag/remember/index',
        tagrememberstore:'/historian-tag/remember/store',
        taguser:'/historian-tag/user',
        tagbindgroup:'/historian-tag/bind-group',
        showmodule: '/historian-module/show/{id}',
        insermodule: '/historian-module/store',
        updatemodule: '/historian-module/update/{id}',
        deletemodule: '/historian-module/destroy/{id}',
        pagemodule: '/historian-module/index'
    },
    historianData: {
        currentdata: '/historian-data/current-data',
        sampleddata: '/historian-data/sampled-data',
        rawdata: '/historian-data/raw-data',
        watchdata: '/historian-data/watch-data'
    },
    group: {
        index: '/tag-group/index',
        show: '/tag-group/show/{id}',
        store: '/tag-group/store',
        update: '/tag-group/update/{id}',
        destroy: '/tag-group/destroy/{id}',
    },
    equipments:{
        equipmentList:'/equipment/index',
        equipmentDetail:'/equipment/show/{id}',
        equipmentAdd:'/equipment/store',
        equipmentUpdate:'/equipment/update/{id}',
        equipmentDelete:'/equipment/destroy/{id}',
        runStopStatistic:'/equipment/run-stop-statistic',
        runStopDetail:'/equipment/run-stop-detail',
        equipmentRecordList:'/equipment/{equipment_id}/maintenance-record/index',
        equipmentRecordDetail:'/equipment/{equipment_id}/maintenance-record/show/{id}',
        equipmentRecordAdd:'/equipment/{equipment_id}/maintenance-record/store',
        equipmentRecordUpdate:'/equipment/{equipment_id}/maintenance-record/update/{id}',
        equipmentRecordDelete:'/equipment/{equipment_id}/maintenance-record/destroy/{id}',
        equipmentParamList:'/equipment/{equipment_id}/param/index',
        equipmentParamDetail:'/equipment/{equipment_id}/param/show/{id}',
        equipmentParamAdd:'/equipment/{equipment_id}/param/store',
        equipmentParamUpdate:'/equipment/{equipment_id}/param/update/{id}',
        equipmentParamDelete:'/equipment/{equipment_id}/param/destroy/{id}',
        equipmentMaintenanceGragh: '/equipment/maintenance-record/gragh'
    },
}
