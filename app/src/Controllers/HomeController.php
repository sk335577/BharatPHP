<?php

namespace App\Controllers;

use BharatPHP\Controller;
use BharatPHP\Request;
use BharatPHP\Response;
use BharatPHP\Config;
use App\Models\Users;
use BharatPHP\Cache;
use BharatPHP\Session;

class HomeController extends Controller
{

    public function home()
    {
        // Cache::put("time","xxx",2);
        // echo Cache::get("time");
        // Session::put("xxx", 's');
        //        Config::setConfig('database.connections.mysql.','ddd');
        //        pd(Config::getAll());die;
        //        (Users::createUser(['name' => 'name', 'email' => 'email', 'password' => 'pass']));
        //        pr(Users::getAllUsers());
        //        (Users::updateUserByUserID(2, ['name' => 'name' . time()]));
        //        pr(Users::getAllUsers());
        //        pd(Users::getAllUsersCount());
        //        Session::put("xxx", 's');
        //        echo t('string');
        //    pr(Session::getAll());
        //        pr(app()->services()->get('browser')->getOs());
        //        echo "xxxxxxx";
        //        pr(app()->services()->get('browser')->getName());
        //        pr(app()->services()->get('browser')->getName());
        //        pr(config());   
        return response(view('home/index', []));
    }
}
