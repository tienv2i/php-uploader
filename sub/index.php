<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$script_path = $_SERVER['SCRIPT_NAME'];

if (strpos($path, $_SERVER['SCRIPT_NAME']) === 0) {
    $path = substr($path, $script_path);
}

$segments = array_filter(explode('/', trim($path,'\\/')));

$path = '/'.implode('/', $segments);

var_dump($segments);