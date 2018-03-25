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
我们创建一个Article Model，这个 Model 对应数据库一张表。此时我们先用mysql 命令行工具新建一个 `demo_database` 的数据库，里面有一张表 `articles` , 表的结构大致如下：

```console
mysql> desc articles;
+---------+--------------+------+-----+---------+----------------+
| Field   | Type         | Null | Key | Default | Extra          |
+---------+--------------+------+-----+---------+----------------+
| id      | int(11)      | NO   | PRI | NULL    | auto_increment |
| title   | varchar(256) | YES  |     | NULL    |                |
| content | varchar(256) | YES  |     | NULL    |                |
+---------+--------------+------+-----+---------+----------------+
3 rows in set (0.00 sec)

```
我们再在表里面填入数据：
```console
mysql> select * from articles;
+----+--------+--------------+
| id | title  | content      |
+----+--------+--------------+
|  1 | hhhhh  | heheheheheh  |
|  2 | hhhhh2 | heheheheh2eh |
+----+--------+--------------+
2 rows in set (0.00 sec)
```
当然了，我们现在是直接通过 MySQL 的 insert 命令直接填入数据，后续我们可以通过我们的框架新建 model 。

接下来我们要做的就是怎么在 Model 中连接数据库取到数据库里面的数据啦！ 本文使用的 php 7.1，我们使用 mysqli 来连接数据库查询数据：

```php
<?php

namespace App\Models;

class Article 
{
    public static function first()
    {
        //mysql_connect is deprecated
        $mysqli = new \mysqli('localhost', 'root', 'w.x.c.123', 'demo_database');
        if ($mysqli->connect_errno) {
            echo "Failed to connect to Mysql: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error();
        }
        $mysqli->set_charset('utf-8');
        $result = $mysqli->query("SELECT * FROM articles limit 0,1");

        $article = $result->fetch_array();
        if ($article) {
            echo "<p>" . $article['title'] . "</p>";
            echo "<p>" . $article['content'] . "</p>";
        }
        $mysqli->close();
    }
}
```
这么一来我们就可以在控制器里面使用 
`
    Article::first();
`
来查询 articles 表里面的第一条article数据，然后我们再通过 echo 返回给浏览器。

```php
<?php

namespace App\Controllers;


use App\Models\Article;

class HomeController extends BaseController
{
    public function home()
    {
        Article::first();
    }

}
```

## View层
看上面的代码，我们在 Article Model 中连续写了两条 echo 语句来格式化输出。如果后续我们的页面十分复杂的时候，我们把所有的格式化输出的代码都写在 Model 里面感觉是个灾难。我们应该把这些格式化输出的代码分离出来，这便是我们说的 MVC 层的 View 层。

我们在 views 目录下新建 home.php: 
```php
<?php

echo "<p>" . $article['title'] . "</p>";
echo "<p>" . $article['content'] . "</p>";
```
 我们再改写一下 Article.php，删除echo 那两行，直接 
 ```php
 return article;
 ```
 然后我们在 HomeController 中指定要使用的 view：
 ```php
<?php

 namespace App\Controllers;
 
 
 use App\Models\Article;
 
 class HomeController extends BaseController
 {
     public function home()
     {
         $article = Article::first();
         require dirname(__FILE__) . "/../views/home.php";
     }
 }

 ```
我们这里的 view 层仅仅是用 PHP 拼接了 html，后续我们需要实现更加复杂的视图的时候，我们可以引入模版引擎。

## ORM
我们一路从一个空文件夹开始，打造一个自己的一个框架，感觉并没有写多少代码，唯一写了好几行代码感觉比较麻烦的就是连接数据库来查询数据了。我们我们有很多 Model，要实现 增删改查的话，我们岂不是要重复 连接，查询、插入、删除、更新，然后关闭连接？我们应该把这些功能分装一下。

怎么封装？有其他人写好的包了，直接拿来用吧～  当然如果你想自己造轮子的话，也可以自己实现一下。

我们这里使用 `illuminate/database`:
```shell
composer require illuminate/database
```
然后我们在 public/index.php 中引入：
```php

use Illuminate\Database\Capsule\Manager as Capsule;

require '../vendor/autoload.php';

// Eloquent ORM
$capsule = new Capsule();
$capsule->addConnection(require '../config/database.php');
$capsule->bootEloquent();

//路由配置
require '../routes/web.php';

```
我们在连接数据的时候，使用了 config/database.php 的数据库配置文件。
```php
<?php

return [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'demo_database',
    'username' => 'root',
    'password' => 'secret',
    'charset' => 'utf8',
    'collation' => 'utf8_general_ci',
    'prefix' => ''
];
```
接下来我们就可以删掉 models/Article.php 文件中我们写的大部分代码，而仅仅需要继承Illuminate\Database\Eloquent\Model 来使用 Eloquent ORM 的功能：
```php
<?php
/**
 * Created by PhpStorm.
 * User: W
 * Date: 24/03/2018
 * Time: 22:23
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Article extends Model
{
    public $timestamps = false;
}

```

更多Eloquent ORM的功能，您也可以自己查阅文档。


## 总结

好了，我们一个 MVC 框架基本上就搭建完了，我们回头看一下整个框架目录结构，是不是跟 Laravel 有点像呢？
```shell
➜  myFirstFramework git:(master) ✗ tree
.
├── README.md
├── app
│   ├── controllers
│   │   ├── BaseController.php
│   │   └── HomeController.php
│   ├── models
│   │   └── Article.php
│   └── views
│       └── home.php
├── composer.json
├── composer.lock
├── config
│   └── database.php
├── public
│   └── index.php
├── routes
│   └── web.php
└── vendor ...
```

ref: https://blog.csdn.net/luyaran/article/details/53836486

