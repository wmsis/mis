
//mysql 简单封装
class MysqlDB {
    constructor(dbConfig) {
        this.mysql = require("mysql")
        this.dbConfig = dbConfig
    }

    query(sql, params) {
        return new Promise((resolve, reject) => {
            const connection = this.mysql.createConnection(
                this.dbConfig
            )
            connection.connect(err => {
                if (err) {
                    console.log("数据库连接失败！")
                    reject(err)
                }
                console.log("数据库连接成功！");
            })
            connection.query(sql, params, (err, results, fileds) => {
                if (err) {
                    console.log("数据库连接失败！")
                    reject(err)
                }
                resolve({ results, fileds })
            })
            connection.end(err => {
                if (err) {
                    console.log("数据库关闭失败！")
                    reject(err)
                }
                console.log("数据库关闭成功！")
            })
        })
    }
}

module.exports = new MysqlDB()
