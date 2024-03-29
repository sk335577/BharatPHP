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

class RegisterController extends Controller
{


    public function doRegistration()
    {

        $post_data = (request()->getPost());
        $post_files = (request()->getFiles());

        $upload_dir = Config::get('registration.upload_files_directory');
        $public_upload_files_url_path = Config::get('registration.public_upload_files_url_path');
        $allowed_types = Config::get('registration.allowed_profile_picture_types');
        $maxsize = Config::get('registration.allowed_profile_picture_size');

        $errors = [];

        // Storing google recaptcha response
        // in $recaptcha variable
        $recaptcha = $post_data['g-recaptcha-response'];

        // Put secret key here, which we get
        // from google console
        $secret_key = Config::get('google_recapthca.sitesecret');

        // Hitting request to the URL, Google will
        // respond with success or error scenario
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
            . $secret_key . '&response=' . $recaptcha;

        // Making request to verify captcha
        $response = file_get_contents($url);

        // Response return by google is in
        // JSON format, so we have to parse
        // that json
        $response = json_decode($response);

        // Checking, if response is true or not
        if (isset($response->success) && $response->success == true) {
        } else {
            $errors[] = 'Invalid form captcha';
        }

        // if (!isset($post_data['__csrf_token__']) || empty(($post_data['__csrf_token__']))) {
        //     //            $errors[] = 'Form expired';
        //     $errors[] = t('registration_form_expiry_error');
        // } else {
        //     if ($post_data['__csrf_token__'] !== csrfToken()) {
        //         //                $errors[] = 'Form expired';
        //         $errors[] = t('registration_form_expiry_error');
        //     }
        // }

        if (!isset($post_data['first_name']) || empty(trim($post_data['first_name']))) {
            $errors[] = 'Invalid firstname';
        } else {
            //            if (preg_match("#^[a-zA-Z]{1,150}$#", $post_data['first_name']) !== 1) {
            if (preg_match("#^.{1,150}$#", $post_data['first_name']) !== 1) {
                $errors[] = 'Invalid first name';
            }
        }

        if (!isset($post_data['last_name']) || empty(trim($post_data['last_name']))) {
            $errors[] = 'Invalid lastname';
        } else {
            //            if (preg_match("#^[a-zA-Z]{1,150}$#", $post_data['last_name']) !== 1) {
            if (preg_match("#^.{1,150}$#", $post_data['last_name']) !== 1) {
                $errors[] = 'Invalid last name';
            }
        }

        if (!isset($post_data['email']) || empty(trim($post_data['email']))) {
            $errors[] = 'Invalid email';
        } else {
            if (!filter_var($post_data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email';
            }
        }

        if (!isset($post_data['phone']) || empty(trim($post_data['phone']))) {
            $errors[] = 'Invalid phone';
        } else {
            //            if (preg_match("#^[0-9A-Za-z]{1,100}$#", $post_data['phone']) !== 1) {
            if (preg_match("#^.{1,100}$#", $post_data['phone']) !== 1) {
                $errors[] = 'Invalid phone';
            }
        }



        if (!isset($post_data['dob']) || empty(trim($post_data['dob']))) {
            $errors[] = 'Invalid dob';
        } else {
            if (preg_match("#^[0-9]{4}[-][0-9]{1,2}[-][0-9]{1,2}$#", $post_data['dob']) !== 1) {
                $errors[] = 'Invalid dob';
            } else {

                $now = time(); // or your date as well
                $passport_validity_date = strtotime($post_data['dob']);

                $diff_days = round(($now - $passport_validity_date) / (60 * 60 * 24));
                $years = round($diff_days / 365, 2);
                if ($years <= 21) {
                    $errors[] = 'You must be over 21 to enter.';
                }
            }
        }





        // if (!isset($post_data['passport_number']) || empty(trim($post_data['passport_number']))) {
        // } else {
        //     //            if (preg_match("#^[0-9A-Za-z]{1,250}$#", $post_data['passport_number']) !== 1) {
        //     if (preg_match("#^.{1,250}$#", $post_data['passport_number']) !== 1) {
        //         $errors[] = 'Invalid passport number';
        //     }
        // }




        // if (!isset($post_data['passport_validity']) || empty(trim($post_data['passport_validity']))) {
        // } else {
        //     if (preg_match("#^[0-9]{4}[-][0-9]{1,2}[-][0-9]{1,2}$#", $post_data['passport_validity']) !== 1) {
        //         $errors[] = 'Invalid passport_validity';
        //     } else {
        //         $now = time(); // or your date as well
        //         $passport_validity_date = strtotime($post_data['passport_validity']);

        //         $diff_days = round(($passport_validity_date - $now) / (60 * 60 * 24));
        //         if ($diff_days <= 1) {
        //             $errors[] = 'Invalid passport_validity';
        //         }
        //     }
        // }




        // Check profile images
        if (
            !isset($post_files['profile_picture']) ||
            empty($post_files['profile_picture'])
        ) {
            $errors[] = "Please provide profile image";
        } else {
            foreach ($post_files['profile_picture']['tmp_name'] as $key => $value) {

                $file_name = $post_files['profile_picture']['name'][$key];
                $file_size = $post_files['profile_picture']['size'][$key];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

                $filepath = $upload_dir . $file_name;

                if (in_array(strtolower($file_ext), $allowed_types)) {
                    if ($file_size > $maxsize) {
                        $errors[] = "Picture size is not valid";
                    }
                } else {
                    $errors[] = "Please provide a valid image";
                }
            }
        }


        if (isset($post_data['consent_accept']) && $post_data['consent_accept'] != '1') {
            $errors[] = 'Invalid form consent';
        }








        $valid_fields = [
            '__csrf_token__',
            'first_name',
            'last_name',
            'email',
            'phone',
            'dob',

            'gender',
            // 'passport_number',
            // 'passport_validity',

            'profile_picture',

            'consent_accept',
            'g-recaptcha-response',

        ];

        foreach ($post_files as $pkey => $pvalue) {
            if (!in_array($pkey, $valid_fields)) {
                $errors[] = 'Invalid field ' . $pkey;
            }
        }

        if (!empty($errors)) {
            return json([
                'status' => 'error',
                'message' => $errors
            ]);
        }








        $database_record = [
            'first_name' => sanitizeStringStripTags($post_data['first_name']),
            'last_name' => sanitizeStringStripTags($post_data['last_name']),
            'email' => $post_data['email'],
            'phone' => sanitizeStringStripTags($post_data['phone']),
            'dob' => $post_data['dob'],

            'gender' => $post_data['gender'],
            // 'passport_number' => $post_data['passport_number'],


            'profile_picture' => '',
            'created_at' => date('Y-m-d H:i:s'),

        ];





        $user_create_response = Users::createUser($database_record);

        if (!isset($user_create_response['status']) || $user_create_response['status'] != 'success') {
            return json([
                'status' => 'error',
                'message' => 'Something went wrong'
            ]);
        }

        $profile_picture = [];

        if (isset($post_files['profile_picture']['tmp_name'])) {

            $upload_url_user_path = $public_upload_files_url_path . "/{$user_create_response['data']['id']}";
            $upload_dir = $upload_dir . DIRECTORY_SEPARATOR . "{$user_create_response['data']['id']}" . DIRECTORY_SEPARATOR;
            mkdir($upload_dir);

            foreach ($post_files['profile_picture']['tmp_name'] as $key => $value) {


                $file_tmpname = $post_files['profile_picture']['tmp_name'][$key];
                $file_name = $post_files['profile_picture']['name'][$key];
                $file_size = $post_files['profile_picture']['size'][$key];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $profile_picture[$key] = [
                    'filename' => $post_files['profile_picture']['name'][$key],
                    'file_size' => $post_files['profile_picture']['size'][$key],
                    'file_ext' => pathinfo($file_name, PATHINFO_EXTENSION),
                ];

                //                $new_file_name = uniqid() . $file_name;
                $new_file_name = uniqid() . "." . $profile_picture[$key]['file_ext'];
                //                $profile_picture[$key]['file_path'] = $filepath;
                $profile_picture[$key]['filename'] = $new_file_name;

                $profile_picture[$key]['file_url'] = $upload_url_user_path . "/" . $new_file_name;

                if (getenv('APP_ENV') == 'local') {
                    //Save files in server
                    $filepath = $upload_dir . $new_file_name;

                    if (move_uploaded_file($file_tmpname, $filepath)) {

                        if (isset($user_create_response['data'])) {

                            //                        $user_create_response['data']['picture_uploaded_info'] = $profile_picture;
                        }
                    } else {
                    }
                    //Save files in server
                } else {
                    //AWS
                    //working
                    $s3 = new \Aws\S3\S3Client([
                        'region' => 'eu-west-1',
                        'version' => 'latest',
                        'endpoint' => config('aws.endpoint'),
                        'credentials' => [
                            'key' => config('aws.key'),
                            'secret' => config('aws.secret'),
                        ]
                    ]);
                    //                $s3_result=[];

                    $s3_result = $s3->putObject([
                        'Bucket' => config('aws.bucket'),
                        'Key' => $new_file_name,
                        'SourceFile' => $file_tmpname
                    ]);


                    if (isset($s3_result['ObjectURL'])) {
                        $profile_picture[$key]['file_url'] = $s3_result['ObjectURL'];
                    }

                    //AWS
                }
            }
            // Users::update(json_encode($profile_picture), $user_create_response['data']['id']);
        }

        //        pd($user_create_response);
        //die;
        // $user_create_response = [];
        return json($user_create_response);
    }

    public function showRegistrationPage()
    {

        return response(view('auth/login/login', [], $layout = 'auth/layouts/auth', $viewtype = 'backend'));
    }
}
