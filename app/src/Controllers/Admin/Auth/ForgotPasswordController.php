<?php

namespace App\Controllers\Admin\Auth;

use App\Controllers\Controller;

use App\Models\Users;
use App\Helpers\Utilities;
use App\Services\Email;
use BharatPHP\Auth;
use BharatPHP\Config;
use PragmaRX\Google2FA\Google2FA;
use BharatPHP\Response;
use BharatPHP\Session;
use BharatPHP\Cookie;

class ForgotPasswordController extends Controller
{
    public function doForgotPassword()
    {



        $post_data = (request()->getPost());

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
            return json(['status' => 'error', 'message' => 'Invalid form']);
        }


        $user = Users::getUserByEmail($post_data['email']);

        if (!empty($user)) {

            while (1) {
                $secret = '';
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                $charactersLength = strlen($characters);
                for ($i = 0; $i < 20; $i++) {
                    $secret .= $characters[mt_rand(0, ($charactersLength - 1))];
                }
                $is_secret_exists = Users::getUserByResetPasswordSecret($secret);
                if (empty($is_secret_exists)) {
                    break;
                }
            }



            Users::updateUserByUserID($user['id'], ['reset_password_secret' => $secret]);
            // Users::updateUserByUserID($user['id'], ['reset_password_secret_generated_time' => time()]);
            Users::updateUserByUserID($user['id'], ['reset_password_secret_generated_time' => date('Y-m-d H:i:s')]);

            $email_vars = array(
                'secret' => $secret,
                'base_url' => appUrl(),
            );

            $body = file_get_contents(BharatPHP_VIEW_PATH . '/backend/auth/forgot-password/email-templates/otp.phtml');

            if (isset($email_vars)) {
                foreach ($email_vars as $k => $v) {
                    $body = str_replace('{' . ($k) . '}', $v, $body);
                }
            }

            Email::sendEmail($post_data['email'], 'Secret Code - Reset Password - ' . Config::get('app_title'), $body);
            return json(['status' => 'success', 'message' => 'If the account exists you should receive a email containing a secret code soon']);
        }


        return json(['status' => 'success', 'message' => 'If the account exists you should receive a email containing a secret code soon']);
    }

    public function doResetPassword()
    {

        if (request()->isPost()) {

            $post_data = (request()->getPost());

            // Storing google recaptcha response
            // in $recaptcha variable
            $recaptcha = $post_data['gcaptcha_token'];

            // Put secret key here, which we get
            // from google console
            $secret_key = Config::get('google_recapthca.sitesecret');

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
                return json(['status' => 'error', 'message' => 'Invalid form']);
            }





            switch ($post_data['action_type']) {

                case 'FORGOT_PASSWORD':

                    $user = Users::getUserByEmail($post_data['email']);

                    if (!empty($user)) {

                        while (1) {
                            $secret = '';
                            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                            $charactersLength = strlen($characters);
                            for ($i = 0; $i < 20; $i++) {
                                $secret .= $characters[mt_rand(0, ($charactersLength - 1))];
                            }
                            $is_secret_exists = Users::getUserByResetPasswordSecret($secret);
                            if (empty($is_secret_exists)) {
                                break;
                            }
                        }



                        Users::updateUserByUserID($user['id'], ['reset_password_secret' => $secret]);
                        Users::updateUserByUserID($user['id'], ['reset_password_secret_generated_time' => time()]);

                        $email_vars = array(
                            'secret' => $secret,
                            'base_url' => appUrl(),
                        );

                        $body = file_get_contents(BharatPHP_VIEW_PATH . '/backend/email/send-reset-password-secret-code.phtml');

                        if (isset($email_vars)) {
                            foreach ($email_vars as $k => $v) {
                                $body = str_replace('{' . ($k) . '}', $v, $body);
                            }
                        }

                        Utilities::sendEmail($post_data['email'], 'Secret Code - Reset Password - ' . Config::get('app_title'), $body);
                        return json(['status' => 'success', 'message' => 'If the account exists you should receive a email containing a secret code soon']);
                    }


                    return json(['status' => 'success', 'message' => 'If the account exists you should receive a email containing a secret code soon']);
                    break;
                case 'RESET_PASSWORD':


                    $user_secret_code_validate = Users::isSecretCodeValid($post_data['secret']);

                    if ($user_secret_code_validate['status'] != 'success') {
                        return json(['status' => 'error', 'message' => 'Secret code is invalid']);
                    }



                    //                    if ((time() - $user_secret_code_validate['data']['reset_password_secret_generated_time']) > 3600) {
                    //                        return json(['status' => 'error', 'message' => 'Secret code is expired']);
                    //                    }



                    $password = trim($post_data['password']);

                    if (strlen($password) >= 6 && $password <= 20) {
                        return json(['status' => 'error', 'message' => 'Password must be between 6 to 20 characters long']);
                    }

                    if (preg_match("#[A-Z]{1,}#", $password) === 1) {
                    } else {
                        return json(['status' => 'error', 'message' => 'Password must contain at least one uppercase']);
                    }


                    if (preg_match("#[a-z]{1,}#", $password) === 1) {
                    } else {
                        return json(['status' => 'error', 'message' => 'Password must contain at least one lowercase']);
                    }


                    if (preg_match("#[0-9]{1,}#", $password) === 1) {
                    } else {
                        return json(['status' => 'error', 'message' => 'Password must contain at least one digit']);
                    }

                    if (preg_match("#[\@\#\$\%&]{1,}#", $password) === 1) {
                    } else {
                        return json(['status' => 'error', 'message' => 'Password must contain special characters from @#$%&']);
                    }



                    $password = md5($post_data['password']);
                    $user_info = Users::getUserByUserID($user_secret_code_validate['data']['id']);

                    if ($user_info['password'] == $password) {
                        return json(['status' => 'error', 'message' => 'You have already used this password']);
                    }


                    if (empty($user_info['password_history'])) {
                        $user_info['password_history'] = json_encode([$user_info['password']]);
                    } else {
                        $password_history = json_decode($user_info['password_history'], 1);

                        if (isset($password_history[0]) && $password_history[0] == $password) {
                            return json(['status' => 'error', 'message' => 'You have already used this password']);
                        }

                        if (isset($password_history[1]) && $password_history[1] == $password) {
                            return json(['status' => 'error', 'message' => 'You have already used this password']);
                        }

                        if (isset($password_history[2]) && $password_history[2] == $password) {
                            return json(['status' => 'error', 'message' => 'You have already used this password']);
                        }

                        //                        if (in_array($password, $password_history)) {
                        //                            return json(['status' => 'error', 'message' => 'You have already used this password']);
                        //                        }

                        array_unshift($password_history, $user_info['password']);
                        //                            $password_history[] = $user_info['password'];
                        $user_info['password_history'] = json_encode($password_history);
                    }



                    //                    return json(['status' => 'error', 'message' => 'Yxxxxrd']);

                    Users::updateUserByUserID($user_secret_code_validate['data']['id'], [
                        "password" => $password,
                        "reset_password_secret" => "",
                        "reset_password_secret_generated_time" => "",
                        "last_password_update_timestamp" => time(),
                        "google2fa" => "",
                        "password_history" => $user_info['password_history'],
                    ]);

                    return json(['status' => 'success', 'message' => 'Your password has been updated successfully']);
                    break;
            }
        }
    }

    public function showForgotPasswordPage()
    {
        return response(view('auth/forgot-password/forgot-password', [], $layout = 'auth/layouts/auth', $viewtype = 'backend'));
    }
}
