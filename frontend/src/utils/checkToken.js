import store from '../store/'
import router from '../router/index.js'
import * as types from '../store/types'
import axios from 'axios';
import request from '../config/request';

export function checkToken(callback){
    let access_expire_time;
    let refresh_expire_time;
    access_expire_time = store.state.token ? store.state.token.access_expire_time : 0;
    refresh_expire_time = store.state.token ? store.state.token.refresh_expire_time : 0;
    let routeName = 'signin';

    let now = (new Date()).getTime();
    if (now < access_expire_time) {
        //token有效
        callback();
    }
    else if (now >= access_expire_time && now < refresh_expire_time) {
        //refresh_time有效
        store.dispatch('showLoading');
        let url = store.state.baseURL + request.auth.refresh;

        axios.get(url, {
            data: {},
            headers: {
                Authorization: `Bearer ${store.state.token.access_token}`
            }
        })
        .then(function (response) {
            store.dispatch('hideLoading');
            if (response.status = 200 && response.data.code == 0) {
                store.dispatch('refresh', {
                    access_token: response.data.data.token,
                    refresh_token: '',
                    expiresIn: 3500
                });
                callback();
            }
            else {
                store.commit(types.LOGOUT);

                router.replace({
                    name: routeName,
                    query: {redirect: router.currentRoute.fullPath}
                });
            }
        })
        .catch(function (error) {
            store.commit(types.LOGOUT);
            router.replace({
                name: routeName,
                query: {redirect: router.currentRoute.fullPath}
            });
            store.dispatch('hideLoading')
        });
    }
    else {
        //失效
        store.commit(types.LOGOUT);

        router.replace({
            name: routeName,
            query: {redirect: router.currentRoute.fullPath}
        });
    }
}
