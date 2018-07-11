# WebMIS
WebMIS基于YII2轻量级、高性能、面向对象的HMVC框架！<br>
Home: http://yii.webmis.vip/<br>
Admin: http://yii.webmis.vip/admin/index<br>
uanme: admin  passwd: admin

# 安装
### 1) 克隆项目
```bash
git clone https://github.com/webmiss/yii yii
```
### 2) 导入数据库
``` bash
public/db/mvc.sql
```
### 3) 更新项目
```bash
composer update
```

# 美化URL
### 1) Apache（public/.htaccess）
```bash
# 编码
AddDefaultCharset UTF-8
<IfModule mod_rewrite.c>
    # 目录浏览
    Options Indexes FollowSymLinks
    # 重写
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php?r=/$1 [QSA,L]
</IfModule>
```

### 2) Nginx
```bash
listen 80;
server_name yii.webmis.cn;

set $root_path '/home/www/yii/public/';
root $root_path;
index index.php index.html;

try_files $uri $uri/ @rewrite;
location @rewrite {
    rewrite ^/(.*)$ /index.php?r=/$1;
}

location ~* ^/(webmis|upload|themes|favicon.png)/(.+)$ {
    root $root_path;
}
```

### Url
```bash
Home: http://localhost/
Admin: http://localhost/admin/index/index
```