<?php

use BharatPHP\Config;
use BharatPHP\App\Src\Services\Database;
use BharatPHP\Browser;
use BharatPHP\Hash\BcryptHasher;

/* Services are global functionalities which can be accessed from any where of the application */

return [
    'services' =>
    [
        // 'config' => [
        //     'call' => '\BharatPHP\App\Src\Services\Config::init',
        //     'params' => [
        //         'config' => BharatPHP_ROOT_PATH . '/app/config/application.php',
        //     ]
        // ],
        // 'database' => [
        //     //    'call' => '\BharatPHP\App\Src\Services\Database',
        //     'call' => '\BharatPHP\App\Src\Services\Database',
        //     'params' => [
        //         'host' => Config::get('database.drivers.mysql.host'),
        //         'user' => Config::get('database.drivers.mysql.user'),
        //         'password' => Config::get('database.drivers.mysql.password'),
        //         'database' => Config::get('database.drivers.mysql.database'),
        //     ]
        // ],
        // 'translator' => [
        //     'call' => '\BharatPHP\App\Src\Services\Translator',
        // //    'params' => [
        // //        'config' => $app->config(),
        // //        'route_params' =>  $app->router()->getParams()
        // //    ]
        // ]
        'browser' => [
            'call' => Browser::class,
            //    'params' => [
            //        'config' => $app->config(),
            //        'route_params' =>  $app->router()->getParams()
            //    ]
        ],
        'hasher_bcrypt' => [
            'call' => BcryptHasher::class,
            //    'params' => [
            //        'config' => $app->config(),
            //        'route_params' =>  $app->router()->getParams()
            //    ]
        ],
    ]
];
