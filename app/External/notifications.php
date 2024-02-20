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
    /*
    // Emal Change -> Again Verify
    'email_change' => function ($hook, $external = null) {

        $title = Helper::lang('notification.email_change_email_title');
        $name = (empty($external['first_name']) ? $external['user_name'] : $external['first_name']);
        $link = '<a href="' . $hook->container->url('/') . '?verify-account=' . $external['token'] . '">
            ' . Helper::lang('base.verify_email') . '
        </a>';
        $body = str_replace(
            ['[USER]', '[VERIFY_LINK]', '[CHANGES]'],
            [$name, $link, $external['changes']],
            (string) Helper::lang('notification.email_change_email_body')
        );

        $email = $hook->addEmail([
            'title' => $title,
            'body' => $body,
            'recipient' => $external['user_name'],
            'recipient_email' => $external['email'],
            'recipient_id' => $external['id'],
            'token' => $external['token']
        ]);

        if ($email)
            return true;
        else
            return null;
    }, */

];
