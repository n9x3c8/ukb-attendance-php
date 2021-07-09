<?php 
session_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: OPTIONS, PUT, GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Accept','application/json');
header('Content-Type: application/json, charset=utf-8');

require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/db.php';
