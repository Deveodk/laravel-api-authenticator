<?php

return [

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'throttle_mail' => [
        'subject' => 'Your account have been disabled',
        'heading' => 'There have been to many login attempts',
        'body' => 'Your account have been deactivated the next two hours',
    ],

    'magic_link_mail' => [
        'subject' => 'Magic login link',
        'heading' => 'Here is your magic login link',
        'body' => 'you can use this link to login to your account. This link will expire in :minutes',
        'actionButton' => 'press to login'
    ],

    'password_reset_mail' => [
        'subject' => 'Password reset link',
        'heading' => 'Here is your password reset link',
        'body' => 'You can use this link to change your password. This link will expire in :minutes',
        'actionButton' => 'press to reset'
    ]
];
