<?php

define('BharatPHP_APP_START', microtime(true));
define("BharatPHP_ROOT_PATH", dirname(dirname(__FILE__)));
define("BharatPHP_VIEW_PATH", BharatPHP_ROOT_PATH . "/app/views");
define("BharatPHP_LANG_PATH", BharatPHP_ROOT_PATH . "/app/lang");
define("BharatPHP_STORAGE_PATH", BharatPHP_ROOT_PATH . "/app/storage");
define("BharatPHP_PUBLIC_PATH", BharatPHP_ROOT_PATH . "/public");
define("BharatPHP_APP_PATH", BharatPHP_ROOT_PATH . "/app");
define("BharatPHP_APP_CONFIG_PATH", BharatPHP_ROOT_PATH . "/app/config");
define("BharatPHP_DATA_PATH", BharatPHP_ROOT_PATH . "/data");

if (getenv('APP_ENV') === false) {
    putenv('APP_ENV=local');
}

require_once BharatPHP_ROOT_PATH . '/BharatPHP/bootstrap.php';

