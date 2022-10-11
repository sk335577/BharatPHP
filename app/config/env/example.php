<?php

return [
    /*
     * Set debugging level
     * 0 - Turn off debugging. Show "Something went wrong" message ambiguously
     * 1 - Show simple error message, file and the line occured
     * 2 - Advanced debugging with code snippet, stack frames, and envionment details
     */
    'debug' => 1,
    'google_recapthca' => [
        'sitekey' => '',
        'sitesecret' => '',
    ],
    //Database Starts Here
    'database' => [
        'default' => 'mysql',
        'connections' => [
            'mysql' => [
                'driver' => 'mysql',
                'host' => '',
                'database' => '',
                'username' => '',
                'password' => '',
            // 'charset' => 'utf8',
// 'collation' => 'utf8_unicode_ci',
// 'prefix' => '',
            ],
            'mysql2' => [
                'driver' => 'mysql',
                'host' => '',
                'database' => '',
                'username' => '',
                'password' => '',
            ]
        ]
    ],
    //Email Sender Settings starts here
    'mail' => [
        'fromName' => '',
        'fromEmail' => '',
        'smtp' => [
            'server' => '',
            'port' => '',
            'security' => '',
            'SMTPAuth' => '',
            'SMTPSecure' => '',
            'username' => '',
            'password' => ''
        ]
    ],
    //Email Sender Settings ends here
    // Set to false to disable sending emails (for use in test environment]
    'useMail' => false
];
