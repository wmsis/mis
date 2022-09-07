export default {
    //以下为公共信息
    loading: false,
    sitename: '伟明MIS',
    sitetitle: '伟明环保设备有限公司',
    host: process.env.VUE_APP_HOST,
    baseURL: process.env.VUE_APP_BASE_URL,

    //以下为商家信息
    userInfo: sessionStorage.getItem('userInfo') ? JSON.parse(sessionStorage.getItem('userInfo')) : '',
    token: sessionStorage.getItem('token') ? JSON.parse(sessionStorage.getItem('token')) : '',
    privileges: sessionStorage.getItem('privileges') ? JSON.parse(sessionStorage.getItem('privileges')) : '',

    //历史曲线、表格记录
    history:{fmfilter:false}
}
