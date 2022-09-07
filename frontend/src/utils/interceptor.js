import axios from 'axios'
import store from '../store/'
import * as types from '../store/types'
import router from '../router/index.js'

// axios 配置
axios.defaults.timeout = 30000;
axios.defaults.baseURL = store.state.baseURL;

// http request 拦截器
axios.interceptors.request.use(
    config => {
        if (store.state.token && store.state.token.access_token) {
            config.headers.Authorization = `Bearer ${store.state.token.access_token}`;
        }

        let fullPath = router.currentRoute.fullPath;
        if(config.method == 'get'){
            //config.params.page_url = fullPath.substring(1);
        }
        else if(config.method == 'post'){
            //config.data.page_url = fullPath.substring(1);
        }

        return config;
    },
    err => {
        return Promise.reject(err);
    });

// http response 拦截器
axios.interceptors.response.use(
    response => {
        if(response && response.data && (response.data.code == 70001 || response.data.code == 80001 || response.data.code == 90001)){
            store.commit(types.LOGOUT);

            router.replace({
                path: 'signin'
            });
        }
        return response;
    },
    error => {
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    store.commit(types.LOGOUT);

                    router.replace({
                        name: 'signin',
                        query: {redirect: router.currentRoute.fullPath}
                    });
            }
            return Promise.reject(error.response.data);
        }
        else{
            return Promise.reject(error);
        }
    });

export default axios;
