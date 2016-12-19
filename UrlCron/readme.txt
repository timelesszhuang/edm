-----------------------------------
UrlCron Ver 1.0 Url定时请求器

运行环境：Windows .Net Framework SP1

Powered By www.XenSystem.Com
-----------------------------------
文件清单：
install.bat　 安装，将UrlCron服务安装到系统Servcie服务中，运行on.bat可马上执行
on.bat　　　  手动启动服务
off.bat　　　 手动停止服务
uninstall.bat 卸载，从服务中删除UrlCron服务
cron.log　　　记录运行日志及请求错误记录
-----------------------------------------------------
cron.ini　　　需要请求的URL清单配置，配置格式：时间(秒) 空格 Url地址，一行为一个任务线程

例如，每60秒访问baidu，每小时访问QQ：

60 http://www.baidu.com/
3600 http://www.qq.com/


-----------------------------------------------------
如因目标网址错误而导致本程序错误停止，请在windows中设置本程序服务失败后仍然重新运行