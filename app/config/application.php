<?php

return array_replace_recursive(
  [
    'app_title' => envConfig('APP_TITLE', 'BharatPHP'),
    'paths' => [],
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
    'application_key' => envConfig('APP_KEY', 'BharatPHP'),
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
    'url' => envConfig('APP_URL'),
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
    'asset_url' => envConfig('ASSET_URL', ''),
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
    'google_recapthca_v3' => [

      'sitekey' => envConfig('GOOGLE_reCAPTCHA_V3_SITE_KEY'),

      'sitesecret' => envConfig('GOOGLE_reCAPTCHA_V3_SITE_SECRET'),
    ],
  ],
  require BharatPHP_APP_CONFIG_PATH . '/views.php',
  require BharatPHP_APP_CONFIG_PATH . '/auth.php',
  require BharatPHP_APP_CONFIG_PATH . '/cache.php',
  require BharatPHP_APP_CONFIG_PATH . '/database.php',
  require BharatPHP_APP_CONFIG_PATH . '/services.php',
  require BharatPHP_APP_CONFIG_PATH . '/events.php',
  require BharatPHP_APP_CONFIG_PATH . '/session.php',
  require BharatPHP_APP_CONFIG_PATH . '/languages.php',
  require BharatPHP_APP_CONFIG_PATH . '/error.php',
  require BharatPHP_APP_CONFIG_PATH . '/mail.php',
);
