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

class ResetPasswordController extends Controller
{


    public function doSendResetPasswordEmail()
    {

        $post_data = (request()->getPost());


        $errors = [];


        $form_fields = [
            'email',
            'gcaptcha_token',
        ];

        foreach ($post_data as $form_key => $form_value) {
            if (!in_array($form_key, $form_fields)) {
                // $errors[] = 'Invalid form fields.';
                $errors[] = 'Invalid login details';
            }
        }

        if (!empty($errors)) {
            return json([
                'status' => 'error',
                'message' => $errors
            ]);
        }

        if (!validateGoogleCaptch('gcaptcha_token')) {
            $errors[] = 'Invalid login details';
        }

        if (!validateEmail('email')) {
            $errors[] = 'Invalid login details';
        }



        if (!empty($errors)) {
            return json([
                'status' => 'error',
                'message' => $errors
            ]);
        }

        $user = Users::getUserByEmail($post_data['email']);


        if (empty($user)) {
            return json(['status' => 'success', 'message' => 'If the account exists you should receive a email containing a secret code soon']);
        }



        while (1) {
            $secret = randomTextGenerator(6);
            $is_secret_exists = Users::getUserByResetPasswordSecret($secret);
            if (empty($is_secret_exists)) {
                break;
            }
        }



        Users::updateUserByUserID($user['id'], ['reset_password_secret' => $secret]);
        Users::updateUserByUserID($user['id'], ['reset_password_secret_generated_time' => date('Y-m-d H:i:s')]);

        $email_vars = array(
            // 'secret' => $secret,
            'otp' => $secret,
            'base_url' => appUrl(),
        );
        $body = getViewFileContentsWithPlaceholders('backend/auth/forgot-password/email-templates/otp.phtml', $email_vars);

        Email::sendEmail($post_data['email'], 'Secret Code - Reset Password - ' . Config::get('app_title'), $body);
        return json(['status' => 'success', 'message' => 'If the account exists you should receive a email containing a secret code soon']);
    }

    public function showResetPasswordPage()
    {
        return response(view('auth/reset-password/reset-password', [], $layout = 'auth/layouts/auth', $viewtype = 'backend'));
    }

    public function doResetPassword()
    {

        $post_data = (request()->getPostSanitized());


        $errors = [];


        $form_fields = [
            'email',
            'secret',
            'password',
            'passwordconfirm',
            'gcaptcha_token',
        ];

        foreach ($post_data as $form_key => $form_value) {
            if (!in_array($form_key, $form_fields)) {
                // $errors[] = 'Invalid form fields.';
                $errors[] = 'Invalid login details';
                break;
            }
        }

        if (!empty($errors)) {
            return json([
                'status' => 'error',
                'message' => $errors
            ]);
        }

        if (!validateGoogleCaptch('gcaptcha_token')) {
            $errors[] = 'Invalid login details';
        }

        if (!validateEmail('email')) {
            $errors[] = 'Invalid login details';
        }



        if ($post_data['password'] != $post_data['passwordconfirm']) {
            $errors[] = 'Invalid form';
        }


        if (!empty($errors)) {
            return json([
                'status' => 'error',
                'message' => $errors
            ]);
        }

        $user = Users::getUserByEmail($post_data['email']);


        if (empty($user)) {
            return json(['status' => 'success', 'message' => 'If the account exists you should receive a email containing a secret code soon']);
        }


        if ($user['email'] != $post_data['email']) {
            $errors[] = 'Invalid form';
        }

        if ($user['reset_password_secret'] != $post_data['secret']) {
            $errors[] = 'Invalid form';
        }


        $user_secret_code_validate = Users::isSecretCodeValid($post_data['secret']);

        if ($user_secret_code_validate['status'] != 'success') {
            return json(['status' => 'error', 'message' => 'Secret code is invalid']);
        }


        if ($user_secret_code_validate['data']['email'] != $post_data['email']) {
            $errors[] = 'Invalid form';
        }

        if ($user_secret_code_validate['data']['email'] != $user['email']) {
            $errors[] = 'Invalid form';
        }



        //                    if ((time() - $user_secret_code_validate['data']['reset_password_secret_generated_time']) > 3600) {
        //                        return json(['status' => 'error', 'message' => 'Secret code is expired']);
        //                    }



        $password = ($post_data['password']);

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



        // $password = md5($post_data['password']);
        $password = hashBcrypt($post_data['password']);

        $user_info = Users::getUserByUserID($user_secret_code_validate['data']['id']);

        $is_password_same_already = hashBcryptVerify($post_data['password'], $user_info['password']);

        if ($is_password_same_already) {
            return json(['status' => 'error', 'message' => ['You have already used this password']]);
        }


        // if ($user_info['password'] == $password) {
        //     return json(['status' => 'error', 'message' => 'You have already used this password']);
        // }


        if (empty($user_info['password_history'])) {
            $user_info['password_history'] = json_encode([$user_info['password']]);
            // $user_info['password_history'] = json_encode([$post_data['password']]);
        } else {
            $password_history = json_decode($user_info['password_history'], 1);
            // pd($password_history[0]);
            $is_password_in_used_list = false;
            foreach ($password_history as $lk => $password_history_hash) {

                if (hashBcryptVerify($post_data['password'], $password_history_hash)) {
                    $is_password_in_used_list = true;
                    break;
                }
                if ($lk + 1 == 3) {
                    break;
                }
            }

            if ($is_password_in_used_list) {
                return json(['status' => 'error', 'message' => 'You have already used this password']);
            }
            // // if (isset($password_history[0]) && $password_history[0] == $password) {
            // if (isset($password_history[0]) && hashBcryptVerify($password, $password_history[0])) {
            //     return json(['status' => 'error', 'message' => 'You have already used this password']);
            // }

            // // if (isset($password_history[1]) && $password_history[1] == $password) {
            // if (isset($password_history[1]) && hashBcryptVerify($password, $password_history[1])) {
            //     return json(['status' => 'error', 'message' => 'You have already used this password']);
            // }

            // // if (isset($password_history[2]) && $password_history[2] == $password) {
            // if (isset($password_history[2]) && hashBcryptVerify($password, $password_history[2])) {
            //     return json(['status' => 'error', 'message' => 'You have already used this password']);
            // }

            // die('aa');
            //                        if (in_array($password, $password_history)) {
            //                            return json(['status' => 'error', 'message' => 'You have already used this password']);
            //                        }

            array_unshift($password_history, $user_info['password']);
            // array_unshift($password_history, $password);
            //                            $password_history[] = $user_info['password'];
            $user_info['password_history'] = json_encode($password_history);
        }



        //                    return json(['status' => 'error', 'message' => 'Yxxxxrd']);

        Users::updateUserByUserID($user_secret_code_validate['data']['id'], [
            "password" => $password,
            "reset_password_secret" => "",
            "is_password_reset_required" => 0,
            "reset_password_secret_generated_time" => "",
            // "last_password_update_timestamp" => time(),
            "last_password_update_timestamp" => date("Y-m-d H:i:s"),
            "auth_2fa" => "",
            "password_history" => $user_info['password_history'],
        ]);

        return json(['status' => 'success', 'message' => 'Your password has been updated successfully']);
    }
}
