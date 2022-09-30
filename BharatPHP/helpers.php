<?php

use BharatPHP\Config;
use BharatPHP\Application;

function config($path, $default = '') {
    return Config::get($path, $default);
}

function appUrl() {
    return Application::app()->request()->appUrl();
}

function printAppUrl() {
    echo Application::app()->request()->appUrl();
}

function getTemplatePart($part, $viewtype = 'frontend') {
    return Application::app()->view()->getTemplatePart($part);
}

function view($view, $params = []) {
    return Application::app()->view()->renderView($view, $params);
}

function printTemplatePart($part, $viewtype = 'frontend') {
    echo Application::app()->view()->getTemplatePart($part);
}
