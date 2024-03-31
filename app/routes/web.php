<?php

use BharatPHP\Router;
use BharatPHP\Routes;



Routes::get('/admin/login', [App\Controllers\Admin\Auth\AuthController::class, 'login'], ['name' => 'show_login_page', 'middleware' => [App\Middleware\RedirectIfAuthenticated::class, \App\Middleware\DynamicConfig::class]]);
Routes::get('/admin/forgot-password', [App\Controllers\Admin\Auth\ForgotPasswordController::class, 'showForgotPasswordPage'], ['name' => 'show_forgot_password_page', 'middleware' => [App\Middleware\RedirectIfAuthenticated::class, \App\Middleware\DynamicConfig::class]]);
Routes::post('/admin/do-forgot-password', [App\Controllers\Admin\Auth\ForgotPasswordController::class, 'doForgotPassword'], ['name' => 'do_forgot_password', 'middleware' => [App\Middleware\RedirectIfAuthenticated::class, \App\Middleware\DynamicConfig::class]]);
Routes::post('/admin/do-login', [App\Controllers\Admin\Auth\AuthController::class, 'doLogin'], ['name' => 'do_login', 'middleware' => []]);
Routes::post('/admin/is-2fa-configured', [App\Controllers\Admin\Auth\AuthController::class, 'checkUserHasConfigured2Fa'], ['name' => 'check_user_has_configured_2Fa', 'middleware' => []]);
//Routes::get('/', [App\Controllers\HomeController::class, 'home'])->middleware();
Routes::get('/', [App\Controllers\HomeController::class, 'home'], ['name' => 'home', 'middleware' => []]);
// Routes::get('/', [App\Controllers\HomeController::class, 'home'], ['name' => 'home', 'middleware' => [\App\Middleware\LoadDataSources::class]]);
Routes::get('/{lang}', [App\Controllers\HomeController::class, 'home'], ['middleware' => []]);
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
