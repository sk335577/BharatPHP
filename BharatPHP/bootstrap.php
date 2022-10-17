<?php

use BharatPHP\Error;
use BharatPHP\Config;

require_once BharatPHP_ROOT_PATH . '/BharatPHP/autoloader.php';

Config::init(include BharatPHP_ROOT_PATH . '/app/config/application.php'); //Save config in config class     

date_default_timezone_set(config('timezone'));

$app = new BharatPHP\Application();

set_exception_handler(function ($e) {
    Error::exception($e);
});

set_error_handler(function ($code, $error, $file, $line) {
    Error::native($code, $error, $file, $line);
});

$app->run();
