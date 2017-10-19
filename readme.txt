

### 项目部署

```
server {
    listen  localhost;
    root    /home/wwwroot/;

    location / {
        index  index.php index.html index.htm;
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ .php$ {
        fastcgi_split_path_info ^(.+.php)(/.+)$;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_index index.php;
        include fastcgi_params;
    }
    location /api { 
        try_files $uri $uri/ /api.php?$query_string; 
    }
    location /admin {
        try_files $uri $uri/ /admin.php?$query_string; 
    }
    location /upload {
        deny all;
        return 404;
    }
    location ~* \.html$ {
        expires -1;
    }
    location ~* \.(css|js|gif|jpe?g|png)$ {
        expires 1M;
        add_header Pragma public;
        add_header Cache-Control "public, must-revalidate, proxy-revalidate";
    }
}
```
### 数据库使用
- 数据库操作手册 ： http://medoo.lvtao.net/
- 参考资料：https://www.lvtao.net/yaf/yaf-case-ext-model.html

### 上传文件操作
- 使用方式： http://document.thinkphp.cn/manual_3_2.html#upload

### Redis 缓存
- https://www.lvtao.net/content/book/redis.htm
- http://7xkx6a.com1.z0.glb.clouddn.com/redis_v2.pdf

### 框架外部扩展，请参考
- 可以利用相关：http://www.initphp.com/

### 学习YAF框架 
- 入口学习：https://www.lvtao.net/sort/yaf/	

备注：内部已经有实例代码。

