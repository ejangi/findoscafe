<?php
/**
 * Index
 */
$path = dirname(realpath(__FILE__));
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', '1');

include $path."/../config.php";
include $path."/../app/request.php";
include $path."/../app/facebook.php";
include $path."/../app/controller.php";
include $path."/../app/app.php";

$app = new App();
$app->run();
?>