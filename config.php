<?php
// example

$host = $_SERVER['HTTP_HOST'];
define('URL_ROOT', "http://{$host}/");
define('URL', "http://{$host}");


$conf = array(
  'callbackUrl' => "http://{$host}/callback.php",
  'consumerKey' => 'YOUR_CONSUMERKEY',
  'consumerSecret' => 'YOUR_CONSUMERSECRET'
);
session_start();