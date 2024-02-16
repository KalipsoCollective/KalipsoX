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
                'title' => 'notification.welcome_email_title',
                'body' => 'notification.welcome_email_body',
                'parameters' => [
                    'user' => $details['u_name'],
                    'link_url' => '/auth/verify-account?token=' . $details['token'],
                    'link_text' => 'auth.verify_account',
                ]
            ]
        ];
    },
    /*
    // Account Recovery Email
    'recovery_request' => function ($hook, $external = null) {

        $external = (array) $external;
        $title = Helper::lang('notification.recovery_request_email_title');
        $name = (empty($external['first_name']) ? $external['user_name'] : $external['first_name']);
        $link = '<a href="' . $hook->container->url('/hesap/kurtar') . '?token=' . $external['token'] . '">
            ' . Helper::lang('base.recovery_account') . '
        </a>';
        $body = str_replace(
            ['[USER]', '[RECOVERY_LINK]'],
            [$name, $link],
            (string) Helper::lang('notification.recovery_request_email_body')
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
    },

    // Account Recovered
    'account_recovered' => function ($hook, $external = null) {

        $external = (array) $external;
        $title = Helper::lang('notification.account_recovered_email_title');
        $name = (empty($external['first_name']) ? $external['user_name'] : $external['first_name']);
        $body = str_replace(
            ['[USER]'],
            [$name],
            (string) Helper::lang('notification.account_recovered_email_body')
        );

        $email = $hook->addEmail([
            'title' => $title,
            'body' => $body,
            'recipient' => $external['user_name'],
            'recipient_email' => $external['email'],
            'recipient_id' => $external['id'],
            'token' => $external['token']
        ]);

        $notification = $hook->notificationsModel->insert([
            'user_id'       => $external['id'],
            'type'          => 'account_recovered',
            'created_at'    => time()
        ]);

        if ($email and $notification)
            return true;
        elseif ($email or $notification)
            return false;
        else
            return null;
    },

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
