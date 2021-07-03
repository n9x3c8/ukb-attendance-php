<?php 
// header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
// header('Access-Control-Allow-Origin: *');
// header('Content-Type: application/json, charset=utf-8');


// grab JSON data sent by Angular
$data = json_decode(file_get_contents('php://input'), true);
// add numeric data
$data["prop3"] = 3;
// reply
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);