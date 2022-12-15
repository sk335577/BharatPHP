<?php

namespace BharatPHP;

use BharatPHP\Router;

class Translator {

    public static function init() {

        $application_lang = Config::get('languages.language');

        $current_lang = (app()->request()->getRouteParam('lang'));

        if (!is_null($current_lang)) {
            $application_lang = $current_lang;
        }

        $languages = Config::get('languages');

        if (!isset($languages['loaded'][$application_lang])) {

            if (!file_exists(Config::get('languages.path') . '/' . $application_lang . ".php")) {
                $application_lang = Config::get('languages.language');
            }

            Config::set(['languages' => [
                    'loaded' => [$application_lang => require_once Config::get('languages.path') . '/' . $application_lang . ".php"],
                    'path' => Config::get('languages.path'),
                    'languages_allowed' => Config::get('languages.languages_allowed'),
                    'language' => $application_lang,
                ]
            ]);
        }
    }

    public static function t($string) {

        $application_lang = config('languages.language');

//        $current_lang = (app()->request()->getRouteParam('lang'));
//        if (!is_null($current_lang)) {
//            $application_lang = $current_lang;
//        }


        return config("languages.loaded.$application_lang.$string");
    }

}
