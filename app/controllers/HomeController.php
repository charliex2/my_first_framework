<?php
/**
 * Created by PhpStorm.
 * User: W
 * Date: 24/03/2018
 * Time: 21:58
 */

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
