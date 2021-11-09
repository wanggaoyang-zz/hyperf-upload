# hyperf-upload

* 安装
composer require hyperf-wgy/upload

* 发布
php bin/hyperf.php vendor:publish hyperf-wgy/upload

* 上传配置：上传驱动 disk 目前仅支持 local or oss

![image](https://user-images.githubusercontent.com/83255932/140857030-3baacb2e-b233-4834-8c97-71c62e47a327.png)

* 文件配置：file.php 使用方式和hyperf上传模块一致

* 异常处理：在AppExceptionHandler中增加; 具体代码在hyperf文档异常模块

![image](https://user-images.githubusercontent.com/83255932/140857388-d1819dc9-bdd1-486a-b81c-9b4a7aec0d53.png)

* 注意：local驱动 保证目录具有权限



