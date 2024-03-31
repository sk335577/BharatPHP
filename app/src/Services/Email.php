<?php

namespace App\Services;

use BharatPHP\Config;
use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    public static function sendEmail($to, $subject, $content)
    {
        $mail = new PHPMailer(true);

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );



        $mail->isSMTP();                                            //Send using SMTP



        // if (getenv('APP_ENV') == 'local' || getenv('APP_ENV') == 'demo') {
        // }

        if (!empty(Config::get('mail.smtp.server'))) {

            $mail->Host = Config::get('mail.smtp.server');
        }



        if (!empty(Config::get('mail.smtp.SMTPAuth'))) {

            $mail->SMTPAuth = Config::get('mail.smtp.SMTPAuth');
        }



        if (!empty(Config::get('mail.smtp.username'))) {

            $mail->Username = Config::get('mail.smtp.username');
        }



        if (!empty(Config::get('mail.smtp.password'))) {

            $mail->Password = Config::get('mail.smtp.password');
        }



        if (!empty(Config::get('mail.smtp.SMTPSecure'))) {

            $mail->SMTPSecure = Config::get('mail.smtp.SMTPSecure');
        }



        if (!empty(Config::get('mail.smtp.port'))) {

            $mail->Port = Config::get('mail.smtp.port');
        }



        if (!empty(Config::get('mail.fromEmail'))) {

            if (!empty(Config::get('mail.fromName'))) {

                $mail->setFrom(Config::get('mail.fromEmail'), Config::get('mail.fromName'));
            } else {

                $mail->setFrom(Config::get('mail.fromEmail'), Config::get('app_title'));
            }
        }




        $mail->addAddress($to);     //Add a recipient












        $mail->isHTML(true);



        //            $subject_template = str_ireplace('{{subject}}', $subject, $subject_template);
        //            $mail->Subject = $post_data['subject'];


        $mail->Subject = $subject;

        // $mail->Body    = 'Secret Code -> <b>' . $secret . '</b>';
        $mail->Body = $content;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
    }
}
