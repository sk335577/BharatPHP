<?php

use BharatPHP\Router;
use BharatPHP\Routes;


$admin_panel_name = config('auth.admin_panel_path');


//Admin Routes
Routes::get("/{$admin_panel_name}", [App\Controllers\Admin\DashboardController::class, 'dashboard'], ['name' => 'show_dashboard_page', 'middleware' => [App\Middleware\AuthGuard::class, \App\Middleware\DynamicConfig::class]]);
Routes::get("/{$admin_panel_name}/login", [App\Controllers\Admin\Auth\AuthController::class, 'login'], ['name' => 'show_login_page', 'middleware' => [App\Middleware\RedirectIfAuthenticated::class, \App\Middleware\DynamicConfig::class]]);
Routes::get("/{$admin_panel_name}/reset-password", [App\Controllers\Admin\Auth\ResetPasswordController::class, 'showResetPasswordPage'], ['name' => 'show_reset_password_page', 'middleware' => [App\Middleware\RedirectIfAuthenticated::class, \App\Middleware\DynamicConfig::class]]);
Routes::post("/{$admin_panel_name}/do-send-reset-password", [App\Controllers\Admin\Auth\ResetPasswordController::class, 'doSendResetPasswordEmail'], ['name' => 'do_send_reset_password', 'middleware' => [App\Middleware\RedirectIfAuthenticated::class, \App\Middleware\DynamicConfig::class]]);
Routes::post("/{$admin_panel_name}/do-reset-password", [App\Controllers\Admin\Auth\ResetPasswordController::class, 'doResetPassword'], ['name' => 'do_reset_password', 'middleware' => [App\Middleware\RedirectIfAuthenticated::class, \App\Middleware\DynamicConfig::class]]);
Routes::post("/{$admin_panel_name}/do-login", [App\Controllers\Admin\Auth\AuthController::class, 'doLogin'], ['name' => 'do_login', 'middleware' => []]);
Routes::get("/{$admin_panel_name}/do-logout", [App\Controllers\Admin\Auth\AuthController::class, 'doLogout'], ['name' => 'do_logout', 'middleware' => []]);



//Frontend Routes
// Routes::post('/'.{$admin_panel_name}.'/is-2fa-configured', [App\Controllers\Admin\Auth\AuthController::class, 'checkUserHasConfigured2Fa'], ['name' => 'check_user_has_configured_2Fa', 'middleware' => []]);
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





// Routes::get('/{lang}/terms-and-conditions', [App\Controllers\TermsAndConditionsController::class, 'showTermsAndConditions'], ['name' => 'show_terms_and_conditions', 'middleware' => [\App\Middleware\VerifyLanguageIsValid::class, \App\Middleware\PrepareAppConfig::class]]);

// Routes::get('/{lang}/verify-vote/{secret_code}', [App\Controllers\RegistrationController::class, 'verifyReviewplayer'], ['name' => 'vote_player_verify_code', 'middleware' => [\App\Middleware\VerifyLanguageIsValid::class, \App\Middleware\PrepareAppConfig::class]]);
// Routes::get('/vote-your-player', [App\Controllers\RegistrationController::class, 'voteplayer'], ['name' => 'vote_player_default', 'middleware' => [\App\Middleware\PrepareAppConfig::class]]);
// Routes::get('/{lang}/vote-your-player', [App\Controllers\RegistrationController::class, 'voteplayer'], ['name' => 'vote_player', 'middleware' => [\App\Middleware\VerifyLanguageIsValid::class, \App\Middleware\PrepareAppConfig::class]]);
// Routes::post('/{lang}/vote-your-player', [App\Controllers\RegistrationController::class, 'submitReviewPlayer'], ['name' => 'submit_vote_player', 'middleware' => [\App\Middleware\VerifyLanguageIsValid::class, \App\Middleware\PrepareAppConfig::class]]);
// Routes::get('/'.{$admin_panel_name}.'', [App\Controllers\Admin\playersController::class, 'listplayers'], ['name' => 'list_players', 'middleware' => [App\Middleware\AuthGuard::class, \App\Middleware\PrepareAppConfig::class]]);
// Routes::get('/'.{$admin_panel_name}.'/votes', [App\Controllers\Admin\ReviewsController::class, 'listReviews'], ['name' => 'list_votes', 'middleware' => [App\Middleware\AuthGuard::class, \App\Middleware\PrepareAppConfig::class]]);
// Routes::get('/'.{$admin_panel_name}.'/export-votes', [App\Controllers\Admin\ReviewsController::class, 'exportReviews'], ['name' => 'export_votes', 'middleware' => [App\Middleware\AuthGuard::class, \App\Middleware\PrepareAppConfig::class]]);
// Routes::get('/login', [App\Controllers\AuthController::class, 'login'], ['name' => 'login', 'middleware' => [App\Middleware\RedirectIfAuthenticated::class, \App\Middleware\PrepareAppConfig::class]]);
// Routes::post('/login', [App\Controllers\AuthController::class, 'doLogin'], ['name' => 'login', 'middleware' => [App\Middleware\RedirectIfAuthenticated::class, \App\Middleware\PrepareAppConfig::class]]);
// Routes::get('/logout', [App\Controllers\AuthController::class, 'logout'], ['name' => 'logout', 'middleware' => [\App\Middleware\PrepareAppConfig::class]]);
// Routes::post('/submit-review', [App\Controllers\Admin\playersController::class, 'submitReview'], ['name' => 'submit_review', 'middleware' => [\App\Middleware\PrepareAppConfig::class]]);
// Routes::post('/player-rating-history', [App\Controllers\Admin\playersController::class, 'playerRatingHistory'], ['name' => 'player_rating_history', 'middleware' => [\App\Middleware\PrepareAppConfig::class]]);
// Routes::get('/forgot-password', [App\Controllers\AuthController::class, 'forgotPassword'], ['name' => 'forgot_password', 'middleware' => [\App\Middleware\PrepareAppConfig::class]]);
// Routes::post('/forgot-password', [App\Controllers\AuthController::class, 'doForgotPassword'], ['name' => 'do_forgot_password', 'middleware' => [\App\Middleware\PrepareAppConfig::class]]);

// Routes::get('/{lang}', [App\Controllers\RegistrationController::class, 'home'], ['name' => 'home', 'middleware' => [\App\Middleware\VerifyLanguageIsValid::class, \App\Middleware\PrepareAppConfig::class]]);
// Routes::get('/', [App\Controllers\RegistrationController::class, 'home'], ['name' => 'home', 'middleware' => [\App\Middleware\PrepareAppConfig::class]]);
// Routes::post('/{lang}/save-participation-details', [App\Controllers\RegistrationController::class, 'savePlayerDetails'], ['name' => 'save_participation_details', 'middleware' => [\App\Middleware\VerifyLanguageIsValid::class, \App\Middleware\PrepareAppConfig::class]]);
