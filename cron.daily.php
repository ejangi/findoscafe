<?php
$path = dirname(realpath(__FILE__));
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', '1');

include $path."/config.php";
include $path."/app/request.php";
include $path."/app/facebook.php";

$me_file = $path.Config::$me_file;
if(!file_exists($me_file)) {
	touch($me_file);
}

$facebook = new Facebook();
$me = $facebook->me();

if(!preg_match('/\{"error"/', $me)) {
	file_put_contents($me_file, $me);
}