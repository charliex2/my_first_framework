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
//    public static function first()
//    {
//        //mysql_connect is deprecated
//        $mysqli = new \mysqli('localhost', 'root', 'w.x.c.123', 'demo_database');
//        if ($mysqli->connect_errno) {
//            echo "Failed to connect to Mysql: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error();
//        }
//        $mysqli->set_charset('utf-8');
//        $result = $mysqli->query("SELECT * FROM articles limit 0,1");
//
//        $array = $result->fetch_array();
//        if ($array) {
//            return $array;
//        }
//        $mysqli->close();
//    }
}
