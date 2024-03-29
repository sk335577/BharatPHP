<?php

namespace App\Middleware;

use BharatPHP\Request;
use BharatPHP\Response;
use BharatPHP\Config;

class DynamicConfig
{

    public function execute(Request $request, Response $response)
    {

        Config::set(['add_any_dynamic_config' => 2 * 2]);
    }
}
