<?php
/**
 * Created by PhpStorm.
 * User: W
 * Date: 24/03/2018
 * Time: 21:49
 */

use Illuminate\Database\Capsule\Manager as Capsule;

require '../vendor/autoload.php';

// Eloquent ORM
$capsule = new Capsule();
$capsule->addConnection(require '../config/database.php');
$capsule->bootEloquent();

//路由配置
require '../routes/web.php';