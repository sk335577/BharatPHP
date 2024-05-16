<?php

use BharatPHP\Error;
use BharatPHP\Config;

error_reporting(-1);

ini_set('display_errors', 'Off');


require_once BharatPHP_ROOT_PATH . '/BharatPHP/autoloader.php';

Config::loadEnvConfig(BharatPHP_APP_CONFIG_PATH . "/env/" . getenv('APP_ENV') . ".env"); //Save config in config class     

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
