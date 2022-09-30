<?php

namespace App\Controllers;

use BharatPHP\Controller;
use BharatPHP\Request;
use BharatPHP\Response;
use BharatPHP\Config;
use App\Models\Users;

class HomeController extends Controller {

    public function home() {

        $this->response()->setBody(view('home/index', []));
        $this->response()->send();
    }

}
