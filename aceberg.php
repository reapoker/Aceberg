<?php
/**
 * Created by PhpStorm.
 * User: silsuer
 * Date: 2018/4/22
 * Time: 10:44
 */


require "./vendor/autoload.php";
require "./Server.php";

$ace = new Server();
$ace->start();


