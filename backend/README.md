windows开机启动
安装
1、npm install pm2 -g   
2、npm install pm2-windows-startup -g   安装windows自启动包
运行
进入项目文件夹
1、pm2-startup install  创建开机启动脚本文件
2、pm2启用项目 pm2 start process.yml
3、保存 pm2 save

设置计划任务
1、开启windows计划任务
2、新建任务每分钟运行根目录bat.crontab.bat脚本文件，重复无限次，开机启动，最高权限运行，无论是否登录都运行
3、确定脚本文件内的目录是否为程序部署的目录，不是需要改成程序运行的目录，PHP环境变量可以添加，或者用绝对路径
