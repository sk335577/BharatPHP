<?php

return [
    'views' => [
        'frontend' => BharatPHP_VIEW_PATH . '/frontend',
        'backend' => BharatPHP_VIEW_PATH . '/backend',
        '404' => ['path' => 'errors/404', 'params' => ['page_title' => 'Error'], 'layout' => 'layouts/default'],
        '500' => ['path' => 'errors/500', 'params' => ['page_title' => 'Error'], 'layout' => 'layouts/default'],
    ],
];
