const Utils = require('./Utils');
const MySql = require('./mysql');
const Connection = MySql.Connection;


//循环获取租户信息
async function getTenementData() {
    let conn;
    try {
        const misConfig = {
            "host": "127.0.0.1",
            "user": "root",
            "password": "64y7nudx",
            "database": "wmmis_system",
            "port": 3306,
            "charset": "UTF8MB4_UNICODE_CI",
            "timezone": "local",
            "connectTimeout": 10000,
            "connectionLimit": 10
        };
        conn = new Connection(misConfig); //连接系统数据库
        let rst = await conn.query('SELECT * FROM tenement', []);
        conn.end();
        return rst;
    } catch (e) {
        console.log('getTenementData出错了，亲');
        console.log(e);
        if(conn){
            conn.end();
        }
        return [];
    }
}


//循环获取电厂抓斗数据库配置信息
async function getConfigGarbageDB(config) {
    let conn;
    try {
        let baseCfg = {
            "port": 3306,
            "charset": "UTF8MB4_UNICODE_CI",
            "timezone": "local",
            "connectTimeout": 10000,
            "connectionLimit": 10
        };

        let dbCfg = {...baseCfg, ...config};
        conn = new Connection(dbCfg); //连接抓斗数据库配置

        let cfgs = await conn.query('SELECT * FROM config_garbage_db WHERE type=?', ['mysql']);
        for(let item of cfgs){
            if(item.type == 'mysql'){
                let dbCfg = {
                    "host": item.ip,
                    "user": item.user,
                    "password": item.password,
                    "database": item.db_name,
                    "port": item.port
                }

                //获取具体电厂抓斗数据
                let datalists = await getGarbageData(dbCfg);
                //获取组织数据
                let orgnization = await conn.query('SELECT * FROM orgnization WHERE id=?', [item.orgnization_id]);

                //将获得的数据插入到本地数据库
                let table = 'grab_garbage_' + orgnization[0].code;
                let now = Utils.formatTime((new Date()).getTime());
                let insertSql = 'INSERT INTO ' + table + '(allsn, sn, time, che, dou, liao, code, lost, hev, created_at, updated_at) VALUES ?';
                let params = [];
                for(let data of datalists){
                    params.push([
                        data.allsn,
                        data.sn,
                        data.time,
                        data.che,
                        data.dou,
                        data.liao,
                        data.code,
                        data.lost,
                        data.hev,
                        now,
                        now
                    ]);
                }

                if(params.length > 0){
                    await conn.query(insertSql, [params]);
                }
            }
        }
        conn.end();
    } catch (e) {
        console.log('getConfigGarbageDB出错了，亲');
        console.log(e);
        if(conn){
            conn.end();
        }
    }
}


//获取具体电厂抓斗数据
async function getGarbageData(config) {
    let conn;
    try {
        let baseCfg = {
            "port": 3306,
            "charset": "UTF8_GENERAL_CI",
            "timezone": "local",
            "connectTimeout": 10000,
            "connectionLimit": 10
        };

        let dbCfg = {...baseCfg, ...config};
        conn = new Connection(dbCfg); //连接具体电厂抓斗数据库

        let date = Utils.formatDate((new Date()).getTime());
        let start = Math.ceil(((new Date(date + ' 00:00:00')).getTime())/1000);
        let end = Math.ceil(((new Date(date + ' 23:59:59')).getTime())/1000);
        let datalists = await conn.query('SELECT * FROM log WHERE time >= ? and time <= ?', [start, end]);

        conn.end();
        return datalists;
    } catch (e) {
        console.log('getGarbageData出错了，亲');
        console.log(e);
        if(conn){
            conn.end();
        }
        return [];
    }
}


//立即执行的匿名函数
(async () => {
    try {
        ////获取租户信息
        let tenements = await getTenementData();
        for(let item of tenements){
            let dbCfg = {
                "host": item.ip,
                "user": item.db_user,
                "password": item.db_pwd,
                "database": item.db_name
            }

            //循环获取电厂抓斗数据库配置信息
            await getConfigGarbageDB(dbCfg)
        }
    } catch (e) {
        console.log('出错了，亲');
        console.log(e);
    }
})();
