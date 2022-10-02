<?php

use BharatPHP\Config;
use BharatPHP\Application;

function config($path, $default = '') {
    return Config::get($path, $default);
}

function appUrl() {
    return app()->request()->appUrl();
}

function printAppUrl() {
    echo app()->request()->appUrl();
}

function getTemplatePart($part, $viewtype = 'frontend') {
    return app()->view()->getTemplatePart($part);
}

function view($view, $params = []) {
    return app()->view()->renderView($view, $params);
}

function app() {
    return Application::app();
}

function response($view, $http_code = 200) {

    app()->response()->setCode($http_code);
    app()->response()->setBody($view);

    return app()->response();
}

function printTemplatePart($part, $viewtype = 'frontend') {
    echo app()->view()->getTemplatePart($part);
}
