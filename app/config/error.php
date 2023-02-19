<?php

return [
    'error' => [
        /*
          |--------------------------------------------------------------------------
          | Error Detail
          |--------------------------------------------------------------------------
          |
          | Detailed error messages contain information about the file in which an
          | error occurs, as well as a PHP stack trace containing the call stack.
          | You'll want them when you're trying to debug your application.
          |
          | If your application is in production, you'll want to turn off the error
          | details for enhanced security and user experience since the exception
          | stack trace could contain sensitive information.
          |
         */
        'detail' => envConfig('DEBUG_APP_ERROR_DETAIL', true),
        /*
          |--------------------------------------------------------------------------
          | Error Logging
          |--------------------------------------------------------------------------
          |
          | When error logging is enabled, the "logger" Closure defined below will
          | be called for every error in your application. You are free to log the
          | errors however you want. Enjoy the flexibility.
          |
         */
        'log' => envConfig('DEBUG_APP_LOG_ERRORS', true)
    ],
];
