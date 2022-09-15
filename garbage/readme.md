pm2运行程序

一、单独运行
进入项目文件夹，命令行运行如下命令
pm2 start ecosystem.config.js

二、windows开机启动
安装
1、npm install pm2 -g   
2、npm install pm2-windows-startup -g   安装windows自启动包
运行
进入项目文件夹
1、pm2-startup install  创建开机启动脚本文件
2、pm2启用项目 pm2 start ecosystem.config.js
3、保存 pm2 save

重启电脑可以查看  pm2 ls
如果要卸载服务，执行 pm2-service-uninstall
