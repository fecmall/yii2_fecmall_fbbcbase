

FecMall
========

> fecmall的安装配置和fecshop的类似，在安装前，建议先看一下, fecmall的安装
文档写的简略一些。

[fecshop安装文档](http://www.fecshop.com/doc/fecshop-guide/develop/cn-1.0/guide-fecshop-about-hand-install.html)

[fecshop配置文档](http://www.fecshop.com/doc/fecshop-guide/develop/cn-1.0/guide-fecshop-about-config.html)

1.安装composer

```
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
composer self-update
```


composer 安装fecshop app advanced


```
composer global require "fxp/composer-asset-plugin:^1.4.4"
composer create-project fecmall/fbbcbase-app-advanced  fecmall_fbbcbase 1.0.0.0
cd fecshop
composer update    
./init
```
初始化完成后进行配置

2.配置`mysql`，`mongodb`，`redis`，`cache`，`session`

在`common/config/main-local.php`中

3.域名解析

```
//vue端域名
vue.fecmall.com

// 经销商后台域名
bbc.appbdmin.fecmall.com

// 平台后台域名
bbc.appadmin.fecmall.com

// vue对应的fecmall api端域名
bbc.appserver.fecmall.com

// 图片域名
bbc.img.fecmall.com   // common image
bbc.img1.fecmall.com  // appfront image
bbc.img2.fecmall.com  // appadmin image
bbc.img3.fecmall.com  // appbdmin image

```

4.nginx配置

请根据自己安装的文件路径填写，例子

```

server {
    listen       80;
    server_name  bbc.appserver.fecmall.com;
    root  /www/web/develop/fecmall/fecmall_fbbcbase/appserver/web;
	server_tokens off;
    include none.conf;
    index index.php index.html index.htm;
    access_log /www/web_logs/access.log wwwlogs;
    error_log  /www/web_logs/error.log  notice;
    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        include fcgi.conf;
    }
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
            expires      30d;
    }
    location ~ .*\.(js|css)?$ {
            expires      12h;
    }
}


server {
    listen     80  ;
    server_name bbc.appbdmin.fecmall.com;
    root  /www/web/develop/fecmall/fecmall_fbbcbase/appbdmin/web;
    server_tokens off;
    include none.conf;
    index index.php index.html index.htm;
    access_log /www/web_logs/access.log wwwlogs;
    error_log  /www/web_logs/error.log  notice;
    location /en/ {
        index index.php;
        if (!-e $request_filename){
                rewrite . /en/index.php last;
        }
    }
    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        include fcgi.conf;
    }
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
            expires      30d;
    }
    location ~ .*\.(js|css)?$ {
            expires      12h;
    }
}



server {
    listen     80  ;
    server_name bbc.appadmin.fecmall.com;
    root  /www/web/develop/fecmall/fecmall_fbbcbase/appadmin/web;
    server_tokens off;
    include none.conf;
    index index.php index.html index.htm;
    access_log /www/web_logs/access.log wwwlogs;
    error_log  /www/web_logs/error.log  notice;
    location /en/ {
        index index.php;
        if (!-e $request_filename){
                rewrite . /en/index.php last;
        }
    }
    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        include fcgi.conf;
    }
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
            expires      30d;
    }
    location ~ .*\.(js|css)?$ {
            expires      12h;
    }
}



server {
    listen     80  ;
    server_name bbc.img.fecmall.com;
	root  /www/web/develop/fecmall/fecmall_fbbcbase/appimage/common;
    server_tokens off;
    include none.conf;
    index index.php index.html index.htm;
    access_log /www/web_logs/access.log wwwlogs;
    error_log  /www/web_logs/error.log  notice;
    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        include fcgi.conf;
    }
	location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
        expires      30d;
    }
}


server {
    listen     80  ;
    server_name bbc.img1.fecmall.com;
	root  /www/web/develop/fecmall/fecmall_fbbcbase/appimage/appserver;
    server_tokens off;
    include none.conf;
    index index.php index.html index.htm;
    access_log /www/web_logs/access.log wwwlogs;
    error_log  /www/web_logs/error.log  notice;
    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        include fcgi.conf;
    }
	location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
        expires      30d;
    }
}

server {
    listen     80  ;
    server_name bbc.img2.fecmall.com;
	root  /www/web/develop/fecmall/fecmall_fbbcbase/appimage/appadmin;
    server_tokens off;
    include none.conf;
    index index.php index.html index.htm;
    access_log /www/web_logs/access.log wwwlogs;
    error_log  /www/web_logs/error.log  notice;
    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        include fcgi.conf;
    }
	location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
        expires      30d;
    }
}

server {
    listen     80  ;
    server_name bbc.img3.fecmall.com;
	root  /www/web/develop/fecmall/fecmall_fbbcbase/appimage/appbdmin;
    server_tokens off;
    include none.conf;
    index index.php index.html index.htm;
    access_log /www/web_logs/access.log wwwlogs;
    error_log  /www/web_logs/error.log  notice;
    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        include fcgi.conf;
    }
	location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
        expires      30d;
    }
}



server {
    listen     80  ;
    server_name vue.fecmall.com;
	root  /www/web/develop/fecmall/vue_fbbcbase_appserver/dist;
    server_tokens off;
    include none.conf;
    index index.php index.html index.htm;
    access_log /www/web_logs/access.log wwwlogs;
    error_log  /www/web_logs/error.log  notice;
    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        include fcgi.conf;
    }
	location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
        expires      30d;
    }
}

```

5.配置store

`@appserver\config\fecshop_local_services\Store.php`

6.图片域名配置文件

配置文件：`@common\config\fecshop_local_services\Image.php`

和上面nginx配置的域名对应好即可，譬如：

```
'appserver' => [
    'basedir'    => '@appimage/appserver',
    'basedomain' => '//bbc.img1.fecmall.com',
],
'appadmin' => [
    'basedir'    => '@appimage/appadmin',
    'basedomain' => '//bbc.img2.fecmall.com',
],
'appbdmin' => [
    'basedir'    => '@appimage/appbdmin',
    'basedomain' => '//bbc.img3.fecmall.com',
],

'common' => [
    'basedir'    => '@appimage/common',
    'basedomain' => '//bbc.img.fecmall.com',
],

```

7.初始化数据库，Yii2 migratge方式导入表

mysql(导入mysql的表，数据，索引):

7.1 fecshop数据库安装

```
./yii migrate --interactive=0 --migrationPath=@fecshop/migrations/mysqldb
```

mongodb(导入mongodb的表，数据，索引):

```
./yii mongodb-migrate  --interactive=0 --migrationPath=@fecshop/migrations/mongodb
```

7.2 fbbcbase数据库安装

```
./yii migrate --interactive=0 --migrationPath=@fbbcbase/migrations/mysqldb
```

mongodb(导入mongodb的表，数据，索引):

```
./yii mongodb-migrate  --interactive=0 --migrationPath=@fbbcbase/migrations/mongodb
```

8.导入测试产品数据以及产品图片

测试数据在  ./tests/data中，可以看到这些文件: appimage.zip  mongo-fecshop_test-2018-11-06.js  mysql_fecshop.sql


8.1导入mongodb测试数据 `mongo-fecshop_test-20170419-065157.js`

```
mongo 127.0.0.1:27017/fecshop --quiet ./mongo-fecshop_test-20170419-065157.js
```

8.2导入mysql，文件为：`mysql_fecshop.sql`，可以使用phpmyadmin导入。

8.3测试产品图片，将`appimage.zip`辅助到fecmall的根目录，覆盖解压

```
unzip -o  appimage.zip
```

9.产品搜索

添加host映射

```
127.0.0.1    xunsearch
```

将`127.0.0.1`替换成你的ip即可

执行脚本：

```
cd vendor/fancyecommerce/fecshop/shell/search
sh fullSearchSync.sh
```

10.

开启nginx mysql mongodb php，你就可以访问本地配置的fecshop了。

平台后台的账户密码为： admin admin123（如果不对，就是123456）

经销商后台：fecshop   fecshop123


### vue端配置

参看：https://github.com/fecmall/vue_fbbcbase_appserver


配置完成后就可以访问vue 前端商城了

http://vue.fecmall.com/#/


### 后台配置

1.网站配置-->基础配置，填写手机号，此处为平台的联系方式。

2.首页配置：填写title 和sku，对应首页的标题的sku列表






### 备注

1.需要把mongodb的产品数据重新导出来一份。

2.mongodb 的配置部分的数据








