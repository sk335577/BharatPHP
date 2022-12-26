<?php

use BharatPHP\Config;
use BharatPHP\Response;
use BharatPHP\Request;

return [
    'events' =>
    [
        'config' => [
            'name' => 'on.app.run',
            'action' => function (BharatPHP\Application $app) {
                
            },
            'params' => []
        ],
        'route_not_found_handler' => [
            'name' => 'on.app.route.not_found',
            'action' => function (BharatPHP\Application $app) {
                // header("HTTP/1.0 404 Not Found");
                // $request = new Request();
                // include(Config::get('views.404'));
                // die();
            },
            'params' => []
        ],
        //    'translation' => [
        //        'name' => 'before.app.controller.method',
        //        'action' => function(BharatPHP\Application $app) {
        //            $app->services()->get('translator')->init($app->configs(), $app->router()->getParams());
        //        },
        //        'params' => [
        //        ]
        //    ],
        'translation' => [
            'name' => 'before.app.controller.method',
            'action' => function (BharatPHP\Application $app) {
                
            },
            'params' => []
        ],
    //        'test' => [
    //            'name' => 'on.app.run',
    //            'action' => function(BharatPHP\Application $app) {
    //                $app->services()->get('database');
    //            },
    //            'params' => [
    //            ]
    //        ]
    ]
];
