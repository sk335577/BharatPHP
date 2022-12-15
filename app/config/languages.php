<?php

return [
    /*
      |--------------------------------------------------------------------------
      | Application Language Config
      |--------------------------------------------------------------------------
     */
    'languages' => [
        /*
          |--------------------------------------------------------------------------
          | Default Application Language
          |--------------------------------------------------------------------------
          |
          | The default language of your application. This language will be used by
          | Lang library as the default language when doing string localization.
          |
         */
        'language' => 'en', //Default Language
        /*
          |--------------------------------------------------------------------------
          | Supported Languages
          |--------------------------------------------------------------------------
          |
          | These languages may also be supported by your application. If a request
          | enters your application with a URI beginning with one of these values
          | the default language will automatically be set to that language.
          |
         */
        'languages_allowed' => ['en', "fr"],
        'path' => BharatPHP_APP_PATH . '/lang'//Directory which contains the languages files
    ],
];
