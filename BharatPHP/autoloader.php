<?php
if (is_file(BharatPHP_ROOT_PATH . '/vendor/autoload.php')) {
    require_once BharatPHP_ROOT_PATH . '/vendor/autoload.php';
} else {
    die('Run Command: composer install');
}
