<?php

namespace BharatPHP;

use BharatPHP\Router;

class Translator {

    public static function t($string) {

        $application_lang = config('languages.language');

        $current_lang = (app()->request()->getRouteParam('lang'));
        if (!is_null($current_lang)) {
            $application_lang = $current_lang;
        }

        $languages = config('languages');

        if (!isset($languages['loaded'][$application_lang])) {
            Config::set(['languages' => ['loaded' => [$application_lang => require_once config('languages.path') . '/' . $application_lang . ".php"]]]);
        }


        return config("languages.loaded.$application_lang.$string");
    }

}
