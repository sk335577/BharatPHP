<?php

return array_replace_recursive(
        [
            'app_title' => 'Title Here',
            'paths' => [
                'views' => [
                    'frontend' => BharatPHP_VIEW_PATH . '/frontend',
                    'backend' => BharatPHP_VIEW_PATH . '/backend',
                    '404' => ['path' => 'errors/404', 'params' => ['page_title' => 'Error'], 'layout' => 'layouts/default'],
                    '500' => ['path' => 'errors/500', 'params' => ['page_title' => 'Error'], 'layout' => 'layouts/default'],
                ],
            ],
            /*
              |--------------------------------------------------------------------------
              | Application Key
              |--------------------------------------------------------------------------
              |
              | This key is used by the encryption and cookie classes to generate secure
              | encrypted strings and hashes. It is extremely important that this key
              | remains secret and it should not be shared with anyone. Make it about 32
              | characters of random gibberish.
              |
             */
            'application_key' => '',
            /*
              |--------------------------------------------------------------------------
              | Application URL
              |--------------------------------------------------------------------------
              |
              | The URL used to access your application without a trailing slash. The URL
              | does not have to be set. If it isn't, we'll try our best to guess the URL
              | of your application.
              |
             */
            'url' => '',
            /*
              |--------------------------------------------------------------------------
              | Asset URL
              |--------------------------------------------------------------------------
              |
              | The base URL used for your application's asset files. This is useful if
              | you are serving your assets through a different server or a CDN. If it
              | is not set, we'll default to the application URL above.
              |
             */
            'asset_url' => '',
            /*
              |--------------------------------------------------------------------------
              | Profiler Toolbar
              |--------------------------------------------------------------------------
              |
              | BharatPHP includes a beautiful profiler toolbar that gives you a heads
              | up display of the queries and logs performed by your application.
              | This is wonderful for development, but, of course, you should
              | disable the toolbar for production applications.
              |
             */
//            'profiler' => false,
            /*
              |--------------------------------------------------------------------------
              | Application Character Encoding
              |--------------------------------------------------------------------------
              |
              | The default character encoding used by your application. This encoding
              | will be used by the Str, Text, Form, and any other classes that need
              | to know what type of encoding to use for your awesome application.
              |
             */
            'encoding' => 'UTF-8',
            /*
              |--------------------------------------------------------------------------
              | Application Timezone
              |--------------------------------------------------------------------------
              |
              | The default timezone of your application. The timezone will be used when
              | BharatPHP needs a date, such as when writing to a log file or travelling
              | to a distant star at warp speed.
              |
             */
            'timezone' => 'UTC',
        ],
        require BharatPHP_APP_CONFIG_PATH . '/auth.php',
        require BharatPHP_APP_CONFIG_PATH . '/database.php',
        require BharatPHP_APP_CONFIG_PATH . '/services.php',
        require BharatPHP_APP_CONFIG_PATH . '/events.php',
        require BharatPHP_APP_CONFIG_PATH . '/session.php',
        require BharatPHP_APP_CONFIG_PATH . '/languages.php',
        require BharatPHP_APP_CONFIG_PATH . '/mail.php',
        require BharatPHP_APP_CONFIG_PATH . '/env/' . getenv('APP_ENV') . '.php',
);
