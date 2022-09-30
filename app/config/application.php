<?php

return array_merge(
        [
            'app_title' => 'Title Here',
            'paths' => [
                'views' => [
                    // 'default' => BharatPHP_VIEW_PATH . '/app/src/views',
                    'default' => BharatPHP_VIEW_PATH,
                    'backend' => BharatPHP_VIEW_PATH . '/backend',
                // '404' => BharatPHP_ROOT_PATH . '/errors/404.phtml',
                //            'app' => BharatPHP_ROOT_PATH . '/app/src/views/application',
                //            'errors' => BharatPHP_ROOT_PATH . '/app/src/views/errors',
                //            'layouts' => BharatPHP_ROOT_PATH . '/app/src/views/layouts'
                ],
            ],
            //Languages Starts Here
            'languages' => [
                'default' => 'en',
                'path' => BharatPHP_ROOT_PATH . '/app/lang'
            ],
            // 'login_cookie_life'
            //Languages Ends Here
            'timezone' => 'UTC'
        //
        //....
        ],
        require __DIR__ . '/env/' . getenv('APP_ENV') . '.php',
        require __DIR__ . '/services.php',
        require __DIR__ . '/events.php'
);
