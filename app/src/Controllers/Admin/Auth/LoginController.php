<?php

namespace App\Controllers\Admin\Auth;

use App\Controllers\Controller;

use App\Models\Users;
use App\Helpers\Utilities;
use BharatPHP\Auth;
use BharatPHP\Config;
use PragmaRX\Google2FA\Google2FA;
use BharatPHP\Response;
use BharatPHP\Session;
use BharatPHP\Cookie;

class LoginController extends Controller
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

        $post_data = (request()->getPost());



        foreach ($post_data as $form_key => $form_value) {
            $post_data[$form_key] = trim($form_value);
        }

        // pd($post_data);

        $errors = [];


        $form_fields = [
            'email',
            'password',
            'gcaptcha_token',
            'auth_2fa_secret',
            // 'google_auth_otp',
            // 'auth_action_type',
            'auth_2fa_otp'
        ];

        foreach ($post_data as $form_key => $form_value) {
            if (!in_array($form_key, $form_fields)) {
                // $errors[] = 'Invalid form fields.';
                $errors[] = 'Invalid login details';
            }
        }



        if (!isset($post_data['email']) || empty($post_data['email'])) {
            // $errors[] = 'Invalid email';
            $errors[] = 'Invalid login details';
        } else {
            if (!filter_var($post_data['email'], FILTER_VALIDATE_EMAIL)) {
                // $errors[] = 'Invalid email';
                $errors[] = 'Invalid login details';
            } else {
                if (preg_match("#^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]{2,}$#", $post_data['email']) !== 1) {
                    // $errors[] = 'Invalid email';
                    $errors[] = 'Invalid login details';
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
            // $errors[] = 'Invalid password';
            $errors[] = 'Invalid login details';
        }



        if (isset($post_data['gcaptcha_token']) && !empty($post_data['gcaptcha_token'])) {

            // Storing google recaptcha response
            // in $recaptcha variable
            $recaptcha = $post_data['gcaptcha_token'];

            // Put secret key here, which we get
            // from google console
            $secret_key = Config::get('google_recapthca_v3.sitesecret');

            // Hitting request to the URL, Google will
            // respond with success or error scenario
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
                . $secret_key . '&response=' . $recaptcha;


            // Making request to verify captcha
            $gresponse = file_get_contents($url);

            // Response return by google is in
            // JSON format, so we have to parse
            // that json
            $gresponse = json_decode($gresponse);


            // Checking, if response is true or not
            if (isset($gresponse->success) && $gresponse->success == true) {
            } else {
                // return json(['status' => 'error', 'message' => 'Invalid form']);
                $errors[] = 'Invalid login details';
            }
        } else {
            $errors[] = 'Invalid login details';
        }





        if (!empty($errors)) {
            return json([
                'status' => 'error',
                'message' => $errors
            ]);
        }



        //Check limit login
        // At the top of page right after session_start();
        if ((Session::has("user_ac_locked"))) {
            $difference = time() - Session::get("user_ac_locked");
            if ($difference > 300) {
                Session::forget("user_ac_locked");
                Session::forget("user_ac_login_attempts");
            }
        }

        if (Session::get("user_ac_login_attempts") > 2) {
            Session::put("user_ac_locked", time());
            return json(['status' => 'error', 'message' => ['Login blocked for 5 minutes']]);
        }
        // }




        $password = md5($post_data['password']);

        if (config('auth.username') == 'email') {
            $user = Users::getUserByEmailAndPassword($post_data['email'], $password);
        } else {
            $user = Users::getUserByUsernameAndPassword($post_data['email'], $password);
        }


        $google2fa = new Google2FA();

        if (!empty($user)) {

            // if (empty($user['last_password_update_timestamp'])) {
            //     $user['last_password_update_timestamp'] = 0;
            // } else {
            //     $user['last_password_update_timestamp'] = (int) ($user['last_password_update_timestamp']);
            // }

            // if ((time() - ($user['last_password_update_timestamp'])) > 7884000) {
            //     return json(['status' => 'error', 'message' => 'Your password is expired. Please reset your password']);
            // }






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
                        Session::put(Auth::user_key, ($user['id']));
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
                        Session::put(Auth::user_key, ($user['id']));
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
            Session::put("user_ac_login_attempts", (Session::get("user_ac_login_attempts", 0) + 1));
        }

        return json(['status' => 'error', 'message' => ['Invalid login details']]);
        // }
    }

    public function login()
    {
        // Session::put("xxx", 's');
        return response(view('auth/login/login', [], $layout = 'auth/layouts/auth', $viewtype = 'backend'));
    }
}
