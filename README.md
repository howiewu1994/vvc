# Virtual Vet Clinic

## 虚拟宠物医院学习系统

Assignment project for ECNU 2017 软件开发实践二.

### 安装

1. 下载安装 PHP + MySQL + Apache 环境 XAMPP:

  https://www.apachefriends.org/download.html -> 选择 7.1.1 / PHP 7.1.1 版本

2. fork一下这个库，放到 C:/xampp/htdocs 下

3. 设置项目根目录 DocumentRoot

  运行XAMPP后点击Config->Apache (httpd.conf)

  ctrl+F 'DocumentRoot', 找到 DocumentRoot "C:/xampp/htdocs" 行，设置为 "C:/xampp/htdocs/vvc/web" (或者你们自己定的）

4. XAMPP里运行Apache, 在浏览器输入 localhost 或者 localhost/index.php 就应该可以访问了项目
