<?php

namespace App\Controllers\Admin\Auth;

use App\Controllers\Controller;

use App\Models\Users;
use App\Helpers\Utilities;
use BharatPHP\Auth;
use BharatPHP\Str;
use BharatPHP\Config;
use PragmaRX\Google2FA\Google2FA;
use BharatPHP\Response;
use BharatPHP\Session;
use BharatPHP\Cookie;
use BharatPHP\Crypter;
use BharatPHP\CrypterV2;

class AuthController extends Controller
{

    public function logout()
    {
        Session::forget(Auth::user_key);
        Response::redirectAndExitAndSaveSession(routeNameToURL('login'));
    }


    public function checkUserHasConfigured2Fa()
    {
    }

    public function doLogin()
    {

        $post_data = (request()->getPostSanitized());



        $errors = [];


        $form_fields = [
            'email',
            'password',
            'gcaptcha_token',
            'auth_2fa_secret',
            'remember',
            // 'auth_action_type',
            'auth_2fa_otp'
        ];

        foreach ($post_data as $form_key => $form_value) {
            if (!in_array($form_key, $form_fields)) {
                $errors[] = 'Invalid form fields.';
                // $errors[] = 'Invalid login details';
            }
        }



        if (!isset($post_data['email']) || empty($post_data['email'])) {
            $errors[] = 'Invalid email';
            // $errors[] = 'Invalid login details';
        } else {
            if (!filter_var($post_data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email';
                // $errors[] = 'Invalid login details';
            } else {
                if (preg_match("#^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]{2,}$#", $post_data['email']) !== 1) {
                    $errors[] = 'Invalid email';
                    // $errors[] = 'Invalid login details';
                } else {
                    //                    if (preg_match("#[._-]@#", $post_data['email']) == 1) {
                    //                        $errors[] = 'Invalid email';
                    //                    } else {
                    //                        if (preg_match("#^[._-]#", $post_data['email']) == 1) {
                    //                            $errors[] = 'Invalid email';
                    //                        }
                    //                    }
                }
            }
        }

        if (!isset($post_data['password']) || empty($post_data['password'])) {
            $errors[] = 'Empty password';
            // $errors[] = 'Invalid login details';
        }



        if (!validateGoogleCaptch('gcaptcha_token')) {
            $errors[] = 'Invalid captcha';
        }





        if (!empty($errors)) {
            return json([
                'status' => 'error',
                'message' => $errors
            ]);
        }


        if (config('auth.use_user_lock_after_failed_password_attemps')) {
            //Check limit login
            // At the top of page right after session_start();
            if ((Session::has("user_ac_locked"))) {
                $difference = time() - Session::get("user_ac_locked");
                if ($difference > config('auth.failed_password_locked_minutes')) {
                    Session::forget("user_ac_locked");
                    Session::forget("user_ac_login_attempts");
                }
            }

            if (Session::get("user_ac_login_attempts") > config('auth.lock_account_after_failed_password_attemps')) {
                Session::put("user_ac_locked", time());
                return json(['status' => 'error', 'message' => ['Login blocked for ' . config('auth.failed_password_locked_minutes') . ' minutes']]);
            }
            // }
        }



        // $password = md5($post_data['password']);


        if (config('auth.username') == 'email') {
            // $user = Users::getUserByEmailAndPassword($post_data['email'], $password);
            $user = Users::getUserByEmail($post_data['email']);
        } else {
            $user = Users::getUserByUsername($post_data['email']);
        }

        if (empty($user)) {
            return json(['status' => 'error', 'message' => ['Invalid login details']]);
        }

        $is_password_correct = hashBcryptVerify($post_data['password'], $user['password']);

        if (!$is_password_correct) {
            return json(['status' => 'error', 'message' => ['Invalid login details']]);
        }


        $google2fa = new Google2FA();

        if (!empty($user)) {

            if (($user['is_password_reset_required'] == 1)) {
                return json(['status' => 'error', 'message' => ['Please reset your password from forgot password page']]);
            }


            if (config('auth.use_password_expiration')) {
                if (config('auth.password_expiration_days') > 0) {
                    if (empty($user['last_password_update_timestamp'])) {
                        $user['last_password_update_timestamp'] = 0;
                    } else {
                        $timestamp_password = strtotime($user['last_password_update_timestamp']);
                        $user['last_password_update_timestamp'] = (int) ($timestamp_password);
                    }

                    if ((time() - ($user['last_password_update_timestamp'])) > config('auth.password_expiration_days')) {
                        return json(['status' => 'error', 'message' => ['Your password is expired. Please reset your password by using forgot password page']]);
                    }
                }
            }




            if (empty($user['auth_2fa'])) {

                if (!isset($post_data['auth_2fa_secret']) && !isset($post_data['google_auth_otp'])) {

                    $secret = $google2fa->generateSecretKey();
                    $text = $google2fa->getQRCodeUrl(
                        // request()->getBaseUrl(),
                        Config::get('app_title'),
                        $user['email'],
                        $secret
                    );

                    $image_url = 'https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=' . $text;

                    //TODO: save secret in session
                    return json(['status' => 'error', 'message' => ['Please setup 2FA Authentication'], 'data' => [
                        'is_2fa_configured' => 0,
                        'auth_2fa_secret' => $secret,
                        // 'google2fa_img' => $g2faUrl,
                        '2fa_img' => $image_url,
                    ]]);
                } else {

                    if ($google2fa->verifyKey($post_data['auth_2fa_secret'], ($post_data['auth_2fa_otp']))) {
                        $update_google_auth_2fa_result = Users::updateUserByUserID($user['id'], ['auth_2fa' => $post_data['auth_2fa_secret']]);
                        if (isset($post_data['remember']) && $post_data['remember'] == 1) {
                            // Auth::remember($user['id']);
                            // $this->rememberLogin();
                            // $token = Crypter::encrypt($user['id'].'|'.Str::random(40));

                            // $this->cookie($this->recaller(), $token, Cookie::forever);
                            Auth::login($user['id'], true);
                        } else {
                            Auth::login($user['id'], false);
                        }
                        // Session::put(Auth::user_key, ($user['id']));
                        Users::updateUserByUserID($user['id'], ['last_login_timestamp' => date("Y-m-d H:i:s")]);
                    } else {
                        return json(['status' => 'error', 'message' => ['Invalid OTP'], 'data' => [
                            'is_google_2fa_otp_valid' => 0,
                        ]]);
                    }
                }
            } else {
                if (!isset($post_data['auth_2fa_otp'])) {
                    return json(['status' => 'error', 'message' => [], 'data' => [
                        'is_2fa_otp_valid' => 0,
                    ]]);
                } else {

                    if ($google2fa->verifyKey($user['auth_2fa'], ($post_data['auth_2fa_otp']))) {

                        // Code is valid
                        Users::updateUserByUserID($user['id'], ['last_login_timestamp' => date("Y-m-d H:i:s")]);
                        // Session::put(Auth::user_key, ($user['id']));
                        if (isset($post_data['remember']) && $post_data['remember'] == 1) {
                            // Auth::remember($user['id']);
                            // $this->rememberLogin();
                            // $token = Crypter::encrypt($user['id'].'|'.Str::random(40));

                            // $this->cookie($this->recaller(), $token, Cookie::forever);
                            Auth::login($user['id'], true);
                        } else {
                            Auth::login($user['id'], false);
                        }
                    } else {
                        return json(['status' => 'error', 'message' => ['Invalid OTP'], 'data' => [
                            'is_2fa_otp_valid' => 0,
                        ]]);
                    }
                }
            }

            //                $current_unix_timestamp = time();
            //
            //                $login_cookie_data = json_encode([
            //                    "iat" => $current_unix_timestamp, // ISSUED AT - TIME WHEN TOKEN IS GENERATED
            //                    // "nbf" => $now, // NOT BEFORE - WHEN THIS TOKEN IS CONSIDERED VALID
            //                    "exp" => $current_unix_timestamp + 3600, // EXPIRY - 1 HR (3600 SECS) FROM NOW IN THIS EXAMPLE
            //                    "jti" => uniqid(), // JSON TOKEN ID
            //                    // "iss" => JWT_ISSUER, // ISSUER
            //                    // "aud" => JWT_AUD, // AUDIENCE
            //                    // WHATEVER USER DATA YOU WANT TO ADD
            //                    "data" => [
            //                        'user_id' => $user['id'],
            //                        'username' => $user['username'],
            //                        'name' => $user['name'],
            //                        'remember_me' => (isset($post_data['remember_me']) && $post_data['remember_me'] == 1) ? 1 : 0,
            //                    ]
            //                ]);
            //
            //                $login_cookie_data = Utilities::encrypt(($login_cookie_data));
            //
            //                $cookie_expiration = 0;
            //                if (isset($post_data['remember_me']) && $post_data['remember_me'] == 1) {
            //
            //                    $cookie_expiration = time() + (86400 * 30); // 86400 = 1 day
            //                }
            //
            //                setcookie('ocld', $login_cookie_data, $cookie_expiration, "/");
            //                Session::put("loggedInUserID", Utilities::encrypt($user['id']));
            //                Session::put("loggedInUserID", ($user['id']));

            return json(['status' => 'success', 'data' => ['is_google_2fa_valid' => 1]]);
        } else {
            if (config('auth.use_user_lock_after_failed_password_attemps')) {
                Session::put("user_ac_login_attempts", (Session::get("user_ac_login_attempts", 0) + 1));
            }
        }

        return json(['status' => 'error', 'message' => ['Invalid login details']]);
        // }
    }

    public function login()
    {
        // Session::put('ss','11111111sx');
        // print_r(Session::getAll());
        return response(view('auth/login/login', [], $layout = 'auth/layouts/auth', $viewtype = 'backend'));
    }
}
