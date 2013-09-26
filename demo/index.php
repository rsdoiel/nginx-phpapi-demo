<?php
header('Content-Type: application/json');
echo json_encode([
    'content' => 'Hello World',
    'sapi_name' => php_sapi_name(),
    'path_info' => $_SERVER['PATH_INFO'],
    //'_SERVER' => $_SERVER
    //'_GET' => $_GET,
    //'_POST' => $_POST,
    'timestamp' => date('r')
]) . PHP_EOL;
?>
