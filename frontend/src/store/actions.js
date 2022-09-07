import * as types from './types.js'
export default {
    //以下为公共信息
    hideLoading: ({ commit, state })=>{
        commit( types.HIDELOADING );
    },
    showLoading: ({ commit, state })=>{
        commit( types.SHOWLOADING );
    },


    //以下为信息
    login: ({ commit, state }, param)=>{
        commit( types.LOGIN, param );
    },
    logout: ({ commit, state })=>{
        commit( types.LOGOUT );
    },
    regist: ({ commit, state })=>{
        commit( types.REGIST );
    },
    userInfo: ({ commit, state }, param)=>{
        commit( types.USERINFO, param );
    },
    refresh: ({ commit, state }, param)=>{
        commit( types.REFRESH, param );
    },
    privileges: ({ commit, state }, param)=>{
        commit( types.PRIVILEGES, param );
    },
    filterHistory:({commit, state}, param)=>{
        commit(types.HISTORY, param)
    }
}
