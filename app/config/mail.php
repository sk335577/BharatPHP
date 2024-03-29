<?php



return [

    'mail' => [

        'contact_form_receiver_email' => envConfig('CONTACT_FORM_ADMIN_EMAIL', ''),

        'contact_form_admin_email_subject_template' => envConfig('CONTACT_FORM_ADMIN_SUBJECT', ''),

        'fromName' => envConfig('EMAIL_FROM_NAME', ''),

        'fromEmail' => envConfig('EMAIL_FROM_EMAIL', ''),

        'smtp' => [

            'server' => envConfig('EMAIL_SMTP_SERVER', ''),

            'port' => envConfig('EMAIL_SMTP_PORT', 587),

            'security' => envConfig('EMAIL_SMTP_SECURITY', 'tls'),

            'SMTPAuth' => true,

            'SMTPSecure' => true,

            'username' => envConfig('EMAIL_SMTP_USERNAME', ''),

            'password' => envConfig('EMAIL_SMTP_PASSWORD', ''),

        ]

    ],

];
