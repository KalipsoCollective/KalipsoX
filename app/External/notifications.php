<?php

/**
 * Notification Hooks 
 **/

use KX\Core\Helper;

return [

    // User Registration Notification
    'welcome' => function ($details) {

        return [
            'notification' => [
                'title' => 'notification.welcome_title',
                'body' => 'notification.welcome_body',
                'url' => null,
                'parameters' => [],
            ],
            'email' => [
                'title' => Helper::lang('notification.welcome_email_title'),
                'body' => Helper::lang('notification.welcome_email_body', [
                    'user' => $details['u_name'],
                    'link_url' => Helper::base('/auth/verify-account?token=' . $details['token']),
                    'link_text' => Helper::lang('auth.verify_account'),
                ]),
            ]
        ];
    },
    'account_verified' => function ($details) {

        return [
            'notification' => [
                'title' => 'notification.account_verified_title',
                'body' => 'notification.account_verified_body',
                'url' => null,
                'parameters' => [],
            ],
            'email' => [
                'title' => Helper::lang('notification.account_verified_email_title'),
                'body' => Helper::lang('notification.account_verified_email_body', [
                    'user' => $details['u_name'],
                    'link_url' => Helper::base('/auth/login'),
                    'link_text' => Helper::lang('auth.login'),
                ]),
            ]
        ];
    },
    'recovery_request' => function ($details) {

        return [
            'notification' => [
                'title' => 'notification.recovery_request_title',
                'body' => 'notification.recovery_request_body',
                'url' => null,
                'parameters' => [],
            ],
            'email' => [
                'title' => Helper::lang('notification.recovery_request_email_title'),
                'body' => Helper::lang('notification.recovery_request_email_body', [
                    'user' => $details['u_name'],
                    'link_url' => Helper::base('/auth/recovery?token=' . $details['token']),
                    'link_text' => Helper::lang('auth.recovery_account'),
                ]),
            ]
        ];
    },
    'recover_success' => function ($details) {

        return [
            'notification' => [
                'title' => 'notification.recover_success_title',
                'body' => 'notification.recover_success_body',
                'url' => null,
                'parameters' => [],
            ],
            'email' => [
                'title' => Helper::lang('notification.recover_success_email_title'),
                'body' => Helper::lang('notification.recover_success_email_body', [
                    'user' => $details['u_name'],
                    'link_url' => Helper::base('/auth/login'),
                    'link_text' => Helper::lang('auth.login'),
                ]),
            ]
        ];
    },
    'email_change' => function ($details) {

        return [
            'notification' => [
                'title' => 'notification.email_change_title',
                'body' => 'notification.email_change_body',
                'url' => null,
                'parameters' => [],
            ],
            'email' => [
                'title' => Helper::lang('notification.email_change_email_title'),
                'body' => Helper::lang('notification.email_change_email_body', [
                    'user' => $details['u_name'],
                    'link_url' => Helper::base('/auth/verify-account?token=' . $details['token']),
                    'link_text' => Helper::lang('auth.verify_account'),
                ]),
            ]
        ];
    },

];
