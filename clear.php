<?php
// example
require_once './config.php';

session_start();
session_destroy();

 
header('Location: ' . URL . '/PATHtoindex.php');