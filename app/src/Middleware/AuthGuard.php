<?php

namespace App\Middleware;

use BharatPHP\Request;
use BharatPHP\Response;
use BharatPHP\Config;
use BharatPHP\Session;

class AuthGuard {

    public function execute(Request $request, Response $response) {

        $logged_in_userID = Session::get('loggedInUserID');

        if (is_null($logged_in_userID)) {
            Response::redirectAndExit(routeNameToURL('login'));
        }
    }

}
