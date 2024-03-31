<?php

return array(
  'session' => [
    /*
          |--------------------------------------------------------------------------
          | Session Driver
          |--------------------------------------------------------------------------
          |
          | The name of the session driver used by your application. Since HTTP is
          | stateless, sessions are used to simulate "state" across requests made
          | by the same user of your application. In other words, it's how an
          | application knows who the heck you are.
          |
          | Drivers: 'cookie', 'file', 'database', 'memcached', 'apc', 'redis'.
          |
         */

    'driver' => 'database',
    /*
          |--------------------------------------------------------------------------
          | Session Database
          |--------------------------------------------------------------------------
          |
          | The database table in which the session should be stored. It probably
          | goes without saying that this option only matters if you are using
          | the super slick database session driver.
          |
         */
    'table' => 'sessions',
    /*
          |--------------------------------------------------------------------------
          | Session Garbage Collection Probability
          |--------------------------------------------------------------------------
          |
          | Some session drivers require the manual clean-up of expired sessions.
          | This option specifies the probability of session garbage collection
          | occuring for any given request to the application.
          |
          | For example, the default value states that garbage collection has a
          | 2% chance of occuring for any given request to the application.
          | Feel free to tune this to your requirements.
          |
         */
    'sweepage' => array(50, 100),
    /*
          |--------------------------------------------------------------------------
          | Session Lifetime
          |--------------------------------------------------------------------------
          |
          | The number of minutes a session can be idle before expiring.
          |
         */
    'lifetime' => envConfig('SESSION_EXPIRE_LIFETIME_IN_MINUTES', 5),
    /*
          |--------------------------------------------------------------------------
          | Session Expiration On Close
          |--------------------------------------------------------------------------
          |
          | Determines if the session should expire when the user's web browser closes.
          |
         */
    // 'expire_on_close' => false,
    'expire_on_close' => envConfig('SESSION_EXPIRE_ON_CLOSE', false),
    /*
          |--------------------------------------------------------------------------
          | Session Cookie Name
          |--------------------------------------------------------------------------
          |
          | The name that should be given to the session cookie.
          |
         */
    'cookie' => 'bp235sesjnjk_',
    /*
          |--------------------------------------------------------------------------
          | Session Cookie Path
          |--------------------------------------------------------------------------
          |
          | The path for which the session cookie is available.
          |
         */
    'path' => envConfig('SESSION_COOKIE_PATH', '/'), // path: The path on the server where the cookie is available.

    /*
          |--------------------------------------------------------------------------
          | Session Cookie Domain
          |--------------------------------------------------------------------------
          |
          | The domain for which the session cookie is available.
          |
         */
    'domain' => envConfig('SESSION_COOKIE_DOMAIN', ''), //example.com
    /*
          |--------------------------------------------------------------------------
          | HTTPS Only Session Cookie
          |--------------------------------------------------------------------------
          |
          | Determines if the cookie should only be sent over HTTPS.
          |
         */
    // 'secure' => false,
    'secure' => envConfig('SESSION_COOKIE_HTTPS_ONLY', false), //secure: If true, the cookie should only be transmitted over secure HTTPS connections.

    'httponly' => envConfig('SESSION_COOKIE_DISABLE_JAVASCRIPT_ACCESS', false), //httponly: If true, the cookie cannot be accessed through JavaScript.

  ]
);
