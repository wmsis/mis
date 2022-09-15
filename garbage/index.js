//const Utils = require('./Utils');
const MySql = require('./mysql');

const config = {
    "host": "10.100.99.66",
    "user": "USER1",
    "password": "123456",
    "database": "ebl_1to4local",
    "port": 3306,
    "charset": "UTF8_GENERAL_CI",
    "timezone": "local",
    "connectTimeout": 10000,
    "connectionLimit": 10
};

const Connection = MySql.Connection;
var conn = new Connection(config);

async function getGarbageData() {
    let rst = await conn.query('SELECT * FROM log WHERE che=?', [1]);
    console.log('111111111111');
    console.log(rst);
    conn.end();
}

getGarbageData();
