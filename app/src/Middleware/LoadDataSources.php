<?php

namespace App\Middleware;

use BharatPHP\Request;
use BharatPHP\Response;
use BharatPHP\Config;

class LoadDataSources
{

    public function execute(Request $request, Response $response)
    {
        Config::set(['content_source' => 'any data']);
    }
}
