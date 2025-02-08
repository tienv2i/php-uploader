<?php
include_once './router.php';

(new Router())
    ->get('/', function ($params = array()) {
        echo "Hello world";
    })
    ->get('/home/([a-zA-Z0-9\-\_]+)/([a-zA-Z0-9\-\_]+)', function ( $params = []) {
        var_dump($params);
    })
    ->dispatch();
