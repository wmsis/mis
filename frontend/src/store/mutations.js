import * as types from './types.js'

export default {
    //以下为公共信息
    [types.HIDELOADING]: ( state )=>{
        state.loading = false;
    },
    [types.SHOWLOADING]: ( state )=>{
        state.loading = true;
    },


    //以下为信息
    [types.LOGIN]: ( state, param )=>{
        var expire = {
            access_token: param.access_token,
            refresh_token: param.refresh_token,
            access_expire_time: (new Date()).getTime() + Number(param.expiresIn) * 1000,
            refresh_expire_time: (new Date()).getTime() + 24 * 60 * 60 * 1000
        }
        sessionStorage.setItem('token', JSON.stringify(expire));
        state.token = expire;
    },
    [types.LOGOUT]: ( state )=>{
        sessionStorage.removeItem('token');
        sessionStorage.removeItem('userInfo');
        sessionStorage.removeItem('privileges');
        state.token = '';
        state.userInfo = '';
        state.privileges = '';
    },
    [types.REGIST]: ( state )=>{

    },
    [types.USERINFO]: ( state, param )=>{
        sessionStorage.setItem('userInfo', JSON.stringify(param.userInfo));
        state.userInfo = param.userInfo;
    },
    [types.REFRESH]: ( state, param )=>{
        var expire = {
            access_token: param.access_token,
            refresh_token: param.refresh_token,
            access_expire_time: (new Date()).getTime() + Number(param.expiresIn) * 1000,
            refresh_expire_time: (new Date()).getTime() + 3600 * 1000 * 24
        }
        sessionStorage.setItem('token', JSON.stringify(expire));
        state.token = expire;
    },
    [types.PRIVILEGES]: ( state, param )=>{
        sessionStorage.setItem('privileges', JSON.stringify(param.privileges));
        state.privileges = param.privileges;
    },
    [types.HISTORY]: (state, param)=>{
      state.history = param
    }
}
