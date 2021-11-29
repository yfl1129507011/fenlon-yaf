```$xslt
基于Yaf框架的后台系统
使用前请完成如下操作：
```
### 下载Yaf扩展
- 去PHP扩展官方网站（http://pecl.php.net）搜索Yaf扩展并下载
- Yaf扩展下载的最终网址：http://pecl.php.net/package/yaf
- 查找对应的Yaf扩展对应的版本（通过phpinfo() 信息查看）
1. php版本号
2. Architecture 信息
3. PHP Extension Build 信息


### 安装Yaf扩展
##### windows下安装
- 下载Yaf扩展解压后，将php_yaf.dll文件复制到PHP的配置文件中（D:\wamp64\bin\php\php7.2.18\ext）。`通过php.ini配置文件中的extension_dir="D:/wamp64/bin/php/php7.2.18/ext/"获取存放扩展的文件路径`
- 在php.ini文件中配置 `extension=yaf`
- 重启web服务器

##### Linux下安装
- 下载Yaf源码[https://github.com/laruence/yaf]
```
[root@localhost ~]# wget https://github.com/laruence/yaf/archive/refs/heads/master.zip
```
- 解压并安装
```
[root@localhost ~]# unzip master.zip 
[root@localhost yaf-master]# cd yaf-master/
[root@localhost yaf-master]# /usr/local/php/bin/phpize 
Configuring for:
PHP Api Version:         20170718
Zend Module Api No:      20170718
Zend Extension Api No:   320170718
[root@localhost yaf-master]# 
[root@localhost yaf-master]# ./configure --with-php-config=/usr/local/php/bin/php-config
[root@localhost yaf-master]# make && make install
```
- 配置php.ini
```
[root@localhost yaf-master]# vim /usr/local/php/etc/php.ini
添加 extension=yaf 后重启php-fpm
```
- 使用代码生成工具yaf_cg来完成这个简单的入门Demo
```
[root@localhost yaf-master]# cd tools/cg/
[root@localhost cg]# php yaf_cg -d Demo
[root@localhost cg]# ll
total 8
drwxr-xr-x. 4 root root   72 Nov 11 17:30 Demo
-rw-r--r--. 1 root root  239 Jul  7 11:06 README.md
drwxr-xr-x. 4 root root  101 Jul  7 11:06 templates
-rwxr-xr-x. 1 root root 3967 Jul  7 11:06 yaf_cg

```

### 在PHPStorm下添加Yaf框架代码的跟踪方式
- 下载官网脚本文件：https://github.com/elad-yosifon/php-yaf-doc
- 运行PHPStorm后，依次选择"File" -> "Settings" -> "PHP" -> 指定“php-yaf-doc”的文件路径