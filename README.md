<a href="https://sales.rntd.cn/"><img src="./docs/首页.png" width="1180" height="480" alt="lazaytools logo"></a>

# 体验地址
- 域名：https://sales.rntd.cn/
- 账号：测试
- 密码：123456




# 部署说明

## 前端部署

> A Vue.js project
- 需要的环境：node.js v12.13.1

# Build Setup
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

### linux服务器

``` bash
1、先搭建LNMP平台，

2、到http://www.yiiframework.com/download/  ，下载yii-advanced-app-2.0.6.tgz包

3、解包

4、把目录移动到网站的根目录下

5、我nginx安装到/usr/local/nginx，php安装到/usr/local/php，所以要修改yii的初始化文件
>> vim /usr/local/nginx/html/advanced/init

把第一行  #!/usr/bin/env php

改成   #!/usr/local/php/bin/php

6、运行init

7、这时/usr/local/nginx/html/advanced/frontend/web/目录就生成了index.php和index-test.php两个文件

8、修改index-test.php，第三行，添加一个客户端IP
如果用服务器访问自身的index-test.php是不需要修改的，但使用客户端去访问就需要添加客户端的ip了。

9、在浏览器中访问，我把/usr/local/nginx/html/advanced/frontend/web/设为网站的根目录，所以直接输入网址就能访问了。看到下图表示成功
```
### windows服务器

``` bash
1、安装wamp2.4以上的版本。

2、到http://www.yiiframework.com/download/  ，下载yii-advanced-app-2.0.6.tgz包

3、解包

4、把目录移动到网站的根目录下

5、运行init.bat
剩余步骤与linux一样

```