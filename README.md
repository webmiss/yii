# WebMIS
WebMIS is just a development idea.<br>
Home: http://yii.webmis.vip/<br>
Admin: http://yii.webmis.vip/admin/index<br>
uanme: admin  passwd: admin

# Install
```bash
Database : public/db/mvc.sql
```

# Configuration
### 1) Apache
```bash
AllowOverride All
Require all granted
Options Indexes FollowSymLinks
```
public/.htaccess
```bash
AddDefaultCharset UTF-8

<IfModule mod_rewrite.c>
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