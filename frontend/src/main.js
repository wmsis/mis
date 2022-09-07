import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store/index.js'
import axios from './utils/interceptor';
import {checkToken} from './utils/checkToken';
import {Message, Notification, MessageBox} from 'element-ui';
import request from './config/request';
import 'iview/dist/styles/iview.css';
import './assets/iconfont/iconfont.css';

Vue.prototype.$request = request;
Vue.prototype.showMessage = function (msg, type = 'warning', time = 3000) {
    Message({
        message: msg,
        type: type,
        duration: time
    });
};

Vue.prototype.notifyInstance = {};
Vue.prototype.showNotification = function (title, msg, type = 'info', time = 3000, click, close) {
    let instance = Notification({
        title: title,
        message: msg,
        type: type,
        duration: time,
        onClick: click || new Function(),
        onClose: close || new Function()
    });

    return instance;
};

Vue.prototype.showMessageBox = function (type, title, content, cb) {
    if (type == 'alert') {
        MessageBox.alert(content, title, {
            confirmButtonText: '确定',
            callback: action => {
                cb(action);
            }
        });
    } else if (type == 'confirm') {
        MessageBox.confirm(content, title, {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
        }).then(() => {
            cb();
        }).catch(() => {

        });
    } else if (type == 'prompt') {
        MessageBox.prompt(content, title, {
            confirmButtonText: '确定',
            cancelButtonText: '取消'
        }).then(({value}) => {
            cb(value);
        }).catch(() => {

        });
    }
};

Vue.prototype.ajax = function (options) {
    if (options.hasOwnProperty('noauth') && options.noauth) {
        ajax(options);
    } else {
        checkToken(() => {
            ajax(options);
        });
    }
};

Vue.prototype.isButtonExist = function (name) {
    let flag = false;
    if (store.state.userInfo.type != 'admin') {
        flag = loopPrivileges(store.state.privileges, name)
    } else {
        flag = true;
    }

    return flag;
};

function ajax(options) {
    let successCallback = options.success || new Function();
    let failCallback = options.fail || new Function();

    axios({
        method: options.method || 'GET',
        url: options.url,
        data: options.data || {},
        params: options.params || {},
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Access-Control-Allow-Origin': '*'
        }
    }).then(function (response) {
        let status = response.status;
        let resultBean = response.data;
        if (status != 200 || resultBean.code != 0) {
            Message({
                message: resultBean.message,
                type: 'warning',
                onClose: failCallback
            });
        } else {
            successCallback(resultBean.data);
        }
    }).catch(function (err) {
        let msg;
        if (err && err.code == 99999) {
            msg = err.message;
        } else {
            msg = '请求异常';
        }

        // Message({
        //     message: msg,
        //     type: 'warning'
        // });
        failCallback(err);
    });
}

function loopPrivileges(list, name){
    let flag = false;
    for(let item of list){
        if (item.type == 'button' && item.api_name && item.api_name == name) {
            flag = true;
            break;
        }
        else if(item.children && item.children.length > 0){
            flag = loopPrivileges(item.children, name);
            if(flag){
                break;
            }
        }
    }

    return flag;
}

new Vue({
    el: '#app',
    router,
    store,
    template: '<App/>',
    components: {App}
});
