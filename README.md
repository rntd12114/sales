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

# 将打包好的部署包上传到指定位置，如/home/appdev/，并解压
# 配置nginx
---------------------------------------------------------------
location / {
        root   /home/appdev/client-vue;
        index  index.html;
        try_files $uri $uri/ /index.php?$query_string;
    }
---------------------------------------------------------------
```


## 服务端部署
数据库配置：


### linux服务器
- 需要的环境： php环境、php-fpm、mysql数据库、nginx、composer

``` bash
#上传./api 目录到服务器任意目录下，如/home/appdev
#修改数据库连接,数据库名称自定义即可：
>> vim ../sales/api/common/config/main.php
---------------------------------------------------------------
		'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:/',
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
>> cd ../sales/api/
>> ./init

# 注意：
- 初始化之后如生成了../sales/api/common/config/main-local.php，请确认该文件中数据库链接信息与main.php保持一致
- 初始化之后如生成了../sales/api/frontend/config/main-local.php，请确认该文件中runtimePath指向的目录是否存在，该目录为日志目录，自行配置即可

# 依赖包初始化
>> cd ../sales/api/
>> composer install
或 composer install --ignore-platform-reqs


#数据结构初始化
>> cd ../sales/api/
>> ./yii migrate
或手动执行/sql/sales.sql
>> mysql -u username -p database_name < sales/sql/sales.sql

#配置nginx，‘/home/appdev’部分请与第一步中上传的目录保持一致
>> vim ./nginx.cof
----------------------------------------------------------------
location ~ \.php$ {
        root           /home/appdev/sales/api/frontend/web/;
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
----------------------------------------------------------------
#重启nginx
>> nginx -s reload
```
