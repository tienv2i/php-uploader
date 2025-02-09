<?php
define('DS', DIRECTORY_SEPARATOR);

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Bootstrap;

// Define configuration settings
$config = [
    'base_dir'     => dirname(__DIR__),
    'public_dir'   => __DIR__,
    'media_dir'    => dirname(__DIR__).DS.'media',
    'template_dir' => dirname(__DIR__).DS.'views',
    'static_dir'   => __DIR__ . DS.'static',
];

Bootstrap::start($config);







