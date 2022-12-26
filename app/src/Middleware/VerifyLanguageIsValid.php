<?php

namespace App\Middleware;

use BharatPHP\Request;
use BharatPHP\Response;
use BharatPHP\Config;
use BharatPHP\Session;

class VerifyLanguageIsValid {

    public function execute(Request $request, Response $response) {
        $lang = getRouteParam('lang');
        $langs = Config::get('languages.languages_allowed');
        $error_404_view = Config::get('views.404');
        if (!in_array($lang, $langs)) {
            app()->response()->setCode(404);
            return app()->response()->setBody(view($error_404_view['path'], $error_404_view['params'], $error_404_view['layout']));
        }
    }

}
