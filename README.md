<a href="https://sales.rntd.cn/"><img src="./docs/首页.png" width="1180" height="480" alt="lazaytools logo"></a>

# 体验地址
- 域名：https://sales.rntd.cn/
- 账号：测试
- 密码：123456




# 部署说明

## 前端部署

> A Vue.js project
- 需要的环境：node.js v12.13.1

### Build Setup
``` bash
# 进入前端目录
>> cd client-vue

# 安装依赖项
>> npm install

# 在本地以热重载方式启动服务 localhost:8080
>> npm run dev

# 打包部署
>> npm run build

# 打包部署同时查看打包详情
>> npm run build --report
```


## 服务端部署
数据库配置：


### linux服务器
- 需要的环境： php环境、php-fpm、mysql数据库、nginx

``` bash
#上传./api 目录到服务器任意目录下，如/home/appdev
#修改数据库连接：
>> vim /home/appdev/api/common/config/main.php
---------------------------------------------------------------
		'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql',
            'username' => 'username',
            'password' => 'password',
            'charset' => 'utf8mb4',
            'attributes' => [
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],
----------------------------------------------------------------
#yii框架初始化
>> cd /home/appdev/api/
>> ./init

#数据结构初始化
>> cd /home/appdev/api/
>> ./yii migrate

#配置nginx，‘/home/appdev’部分请与第一步中上传的目录保持一致
>> vim ./nginx.cof
----------------------------------------------------------------
location ~ \.php$ {
        root           /home/appdev/api/frontend/web/;
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
----------------------------------------------------------------
#重启nginx
>> nginx -s reload
```
