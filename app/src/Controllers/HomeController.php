<?php

namespace App\Controllers;

use BharatPHP\Controller;
use BharatPHP\Request;
use BharatPHP\Response;
use BharatPHP\Config;
use App\Models\Users;
use BharatPHP\Session;

class HomeController extends Controller {

    public function home() {
//        Session::put("xxx", 's');
        echo tx('string');
        return response(view('home/index', []));
    }

}
