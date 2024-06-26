<?php

namespace App\Middleware;

use BharatPHP\Auth;
use BharatPHP\Request;
use BharatPHP\Response;
use BharatPHP\Config;
use BharatPHP\Session;

class RedirectIfAuthenticated
{

    public function execute(Request $request, Response $response)
    {

        if (!Auth::guest()) {
            return app()->response()->redirect(routeNameToURL('show_dashboard_page'));
            // Response::redirectAndExit(routeNameToURL('show_dashboard_page'));
        }
    }
}
