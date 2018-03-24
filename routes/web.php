<?php
/**
 * Created by PhpStorm.
 * User: W
 * Date: 24/03/2018
 * Time: 21:51
 */

use NoahBuscher\Macaw\Macaw;

Macaw::get('hello', function () {
    echo "hello world";
});

Macaw::get('', 'App\\Controllers\\HomeController@home');

Macaw::dispatch();