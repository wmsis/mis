'use strict'
const merge = require('webpack-merge')
const prodEnv = require('./prod.env')
//永强电厂IP:10.100.100.1
//本地测试:http://api.yqsis.com/api
module.exports = merge(prodEnv, {
  NODE_ENV: '"development"',
  VUE_APP_BASE_URL: '"http://mis.com/api"',
  VUE_APP_HOST: '"http://mis.com/"'
})
