<?php

use BharatPHP\Router;
use BharatPHP\Routes;

//Routes::get('/', [App\Controllers\HomeController::class, 'home'])->middleware();
Routes::get('/', [App\Controllers\HomeController::class, 'home'], ['name' => 'home', 'middleware' => [App\Middleware\VerifyCsrfToken::class]]);
Routes::get('/{lang}', [App\Controllers\HomeController::class, 'home'], ['middleware' => [App\Middleware\VerifyCsrfToken::class]]);
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
