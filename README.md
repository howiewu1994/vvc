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

### 目录结构说明

* debug - 日志

* src - 所有的 PHP，SQl 和 Twig（html）代码（页面布局），按照MVC分
  * Controller - PHP控制类
  * Model - 数据库操作和数据结构
  * View - 页面布局模板
  * View 下的 templates - 主要模板 - homepage, login, catalog, navigation, etc.
  * templates 下的 inc - 小功能模板，这些被主要模板包含着 - header, footer, nav etc
  
* tests - 测试相关的

* uploads - 病例中使用的图片和视频

* vendor - PHP 应用框架

* web - CSS, Javascript 和其他前端代码 + 文件（如页面布局的图片）

* db.sql - 创建初始化数据库的SQL语句

### Twig 模板说明

为了不ctrl+C - ctrl+V 很多代码利用模板。Twig的一行代码等于简化的一行或者多行PHP代码，有助于PHP开发者，又有助于html-css-js开发者。

用模板可以吧每一页的每个小布局分离出来到独立的文件去（inc文件夹），以后include进来就可以了。这样可以多两重用代码和简化逻辑。
在项目当中把前端代码引入到Twig的 {% block %} 和 {% endblock %} 之间就行了。纤细的内容看http://twig.sensiolabs.org/doc/2.x/templates.html#template-inheritance

Twig 文件可以有任何扩展名，但是为了保持统一性，我们还是来用.twig 扩展名。

Twig语法高亮显示: http://twig.sensiolabs.org/doc/2.x/templates.html#ides-integration
