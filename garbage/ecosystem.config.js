module.exports = {
  apps : [{
    name: 'TCP',
    script: './index.js',

    // Options reference: https://pm2.io/doc/en/runtime/reference/ecosystem-file/
    args: '',
    instances: 'max',
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      NODE_ENV: 'development'
    },
    env_production: {
      NODE_ENV: 'production'
    }
  }],

  deploy : {
    production : {
      user : 'root',  // SSH user
      host : '47.101.169.146',
      ref  : 'origin/master',
      repo : 'git@github.com:wenhuaWeiming/node_tcp.git', // Github上的仓库地址
      path : '/www/node-tcp',
      'post-deploy' : 'npm install && chown -R root:root /www/node-tcp && pm2 reload ecosystem.config.js --env production'
    }
  }
};
