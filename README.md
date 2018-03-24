# 从头开始撸一个PHP框架

随着PHP标准和Composer包管理工具的面世，普通开发者撸一个框架已经不再是什么难事了。

无论是路由管理、ORM管理、还是视图渲染都有许许多多优秀的包可以使用。我们就像堆积木一样把这些包用composer一个个堆积起来。

接下来我们便是简单地实现一个MVC框架，来加深我们对框架的理解。

## composer
创建一个空的 `composer.json` 文件。
```json
{
  
}
```
或者在空目录下执行：
```shell
composer init
```
则可以生成一个类型如下的文件：

```json
{
    "name": "charlie/my_first_framework",
    "description": "My First Framework",
    "type": "project",
    "license": "MIT",
    "authors": [
        {
            "name": "Charlie",
            "email": "demo@qq.com"
        }
    ],
    "minimum-stability": "dev",
    "require": {}
}
```

## 安装第一个包
我们接下来安装一个管理路由的包： `noahbuscher/macaw`。 功能比这个更加强大的路由包有很多，但是为了简单起见，我们选择安装这个包。

```shell
composer require noahbuscher/macaw
```

当前目录结构为：
```shell
➜  demo tree
.
├── composer.json
├── composer.lock
└── vendor
    ├── autoload.php
    ├── composer
    │   ├── ClassLoader.php
    │   ├── LICENSE
    │   ├── autoload_classmap.php
    │   ├── autoload_namespaces.php
    │   ├── autoload_psr4.php
    │   ├── autoload_real.php
    │   ├── autoload_static.php
    │   └── installed.json
    └── noahbuscher
        └── macaw
            ├── LICENSE.md
            ├── Macaw.php
            ├── README.md
            ├── composer.json
            ├── composer.lock
            └── config
                ├── Web.config
                └── nginx.conf
```

## public/index.php

我们在根目录下新建一个public文件夹，并在该文件夹下新建 `index.php`。`index.php` 文件类似于一个大厦的入口，我们所有的请求都运行 `index.php`。

下面是 `index.php` 文件的代码：
```php
// 自动加载vendor目录下的包
require '../vendor/autoload.php';

```

## routes.php
此时我们观察 `index.php`，除了把vendor下面的包都 require 进来了外，其他啥都没干。那么如何响应各种各样的请求呢？

我们需要定义路由。路由就有点像快递分拣站，把写着不同地址的请求分拨给不同的控制器处理。
那么我们在根目录下创建一个  `routes` 文件夹，并在该文件夹下创建 `web.php` 文件。文件内容：

```php
<?php

use NoahBuscher\Macaw\Macaw;

Macaw::get('hello', function () {
    echo "hello world";
});

Macaw::dispatch();

```
然后我们启动php内置的开发服务器：
```shell
> cd public

> php -S localhost:8001
```
我们访问 `http://localhost:8001/hello` 就能看到我们预期的 "hello world".


### MVC
上面我们仅仅实现了访问一个地址，返回一个字符串。下面我们来真正搭建MVC框架。MVC其实就是Model、View、Controller三个的简称。
不管怎么样，我们先新建三个文件夹再说，即 `views`、`models`、`controllers`。
 
 新建 controllers\HomeController.php 文件，代码如下：
 
 ```php
<?php
 
 namespace App\Controllers;
 
 
 use App\Models\Article;
 
 class HomeController extends BaseController
 {
     public function home()
     {
       echo "<h1>This is Home Page</h1>";
     }
 
 }
 ```
 另外我们在 `routes/web.php` 中添加一条路由：
 ```php
 Macaw::get('', 'App\\Controllers\\HomeController@home');
 ```
 整体代码为：
 ```php
 <?php

 use NoahBuscher\Macaw\Macaw;
 
 Macaw::get('hello', function () {
     echo "hello world";
 });
 
 Macaw::get('', 'App\\Controllers\\HomeController@home');
 
 Macaw::dispatch();
 ```
 
此时已经绑定了一个路由至我们一个控制器的方法，但是我们去访问 `http://localhost:8001` 会出现 ` Uncaught Error: Class 'App\Controllers\HomeController' not found in `的错误。

为什么呢？

因为此时我们还并没有将控制器加载进来，程序并不知道控制器在哪儿。我们可以用 composer 的 classmap 方法加载进来。修改 composer.json 中添加：
```json
 "autoload": {
    "classmap": [
      "app/controllers",
      "app/models"
    ]
  }
```
我们顺便把models也加载进来。

```shell
composer dump-autoload
```
刷新自动加载

## Model连接数据库
我们创建一个Article Model，这个 Model 对应数据库一张表。


ref: https://blog.csdn.net/luyaran/article/details/53836486

