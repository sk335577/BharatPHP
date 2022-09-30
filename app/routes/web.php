<?php

use BharatPHP\Router;

Router::get('/', [App\Controllers\HomeController::class, 'home']);
// Router::get('/register', [Home::class, 'register']);
// Router::post('/register', [Home::class, 'register']);
// Router::get('/login', [Home::class, 'login']);
// Router::get('/login/{id}', [Home::class, 'login']);
// Router::post('/login', [Home::class, 'login']);
// Router::get('/logout', [Home::class, 'logout']);
// Router::get('/contact', [Home::class, 'contact']);
// Router::get('/about', [AboutController::class, 'index']);
// Router::get('/profile', [Home::class, 'profile']);
// Router::get('/profile/{id:\d+}/{username}', [Home::class, 'login']);
