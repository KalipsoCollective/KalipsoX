<?php

/**
 * @package KX
 * @subpackage Controller\Notification
 */

declare(strict_types=1);

namespace KX\Controller;

use KX\Core\Helper;


use KX\Core\Request;
use KX\Core\Response;

use KX\Model\Emails;
use KX\Model\Users;
use KX\Model\Notifications;


final class Notification
{

    public function getNotificationHook()
    {
        $file = Helper::path('app/External/notifications.php');
        if (file_exists($file)) {
            return require $file;
        } else {
            return [];
        }
    }

    public function createNotification($type, $data)
    {
        $hook = $this->getNotificationHook();
        if (isset($hook[$type])) {
            $hook = $hook[$type];
            $details = $hook($data);

            // create notification
            if (isset($details['notification']) !== false) {
                $notificationModel = new Notifications();
                $insertNotification = $notificationModel->insert([
                    'user_id' => $data['id'],
                    'type' => $type,
                    'details' => json_encode($details['notification']),
                    'created_at' => time()
                ]);
            }

            // create email notification
            if (isset($details['email']) !== false) {
                $emailModel = new Emails();
                $insertEmail = $emailModel->insert([
                    'user_id' => $data['id'],
                    'type' => $type,
                    'details' => json_encode($details['email']),
                    'created_at' => time()
                ]);
            }

            if ($insertNotification && $insertEmail) {
                return true;
            }
        }
        return false;
    }

    public function sendEmails($limit = 20)
    {

        $emailList = [];
        $sendList = [];

        $emailModel = new Emails();
        $emails = $emailModel->select('id, user_id, type, details, created_at')
            ->where('status', 'pending')
            ->limit($limit * 4)
            ->orderBy('created_at', 'DESC')
            ->getAll();

        if (!empty($emails)) {

            foreach ($emails as $email) {

                // time check - 6 hours
                if ((time() - $email->created_at) > 60 * 60 * 6) {
                    $emailList[$email->id] = [
                        'uncompleted'
                    ];
                    continue;
                }

                $userModel = new Users();
                $user = $userModel
                    ->select('email, f_name, l_name, u_name')
                    ->where('id', $email->user_id)
                    ->notWhere('status', 'deleted')
                    ->get();
                if (!empty($user)) {
                    $details = json_decode($email->details);
                    $sendList[$email->id] = [
                        'email' => $user->email,
                        'title' => $details->title,
                        'body' => $details->body,
                        'recipient' => $user->f_name . ' ' . $user->l_name === ' ' ? $user->u_name : trim($user->f_name . ' ' . $user->l_name),
                    ];
                } else {
                    $emailList[$email->id] = [
                        'status' => 'uncompleted'
                    ];
                }

                if (count($sendList) >= $limit) {
                    break;
                }
            }
        }

        if (!empty($sendList)) {

            foreach ($sendList as $id => $emailDetails) {
                $send = $this->sendEmail($emailDetails['email'], $emailDetails['title'], $emailDetails['body'], $emailDetails['recipient']);

                if ($send) {
                    $emailList[$id] = [
                        'status' => 'completed',
                        'email' => $emailDetails['email'],
                    ];
                }
            }
        }

        if (!empty($emailList)) {
            foreach ($emailList as $id => $up) {
                $up['updated_at'] = time();
                $emailModel
                    ->where('id', $id)
                    ->update($up);
            }
        }

        return [
            'send' => $sendList,
            'update' => $emailList
        ];
    }

    public function sendEmail($email, $title, $body, $recipient = null)
    {
        // pass if dev mode
        if (Helper::config('DEV_MODE', true)) {
            return true;
        }

        // send email
        if (Helper::config('settings.mail_send_type') === 'server') { // server

            $headers = "From: " . Helper::config('settings.smtp_email_address') . "\r\n";
            $headers .= "Reply-To: " . Helper::config('settings.contact_email') . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            try {
                return mail($email, $title, $body, $headers);
            } catch (\Exception $e) {
                return false;
            }
        } else { // smtp
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                //Server settings
                $mail->isSMTP();
                $mail->Host = Helper::config('settings.smtp_address');
                $mail->SMTPAuth = true;
                $mail->Username = Helper::config('settings.smtp_email_address');
                $mail->Password = Helper::config('settings.smtp_email_password');
                $mail->SMTPSecure = Helper::config('settings.smtp_secure');
                $mail->Port = Helper::config('settings.smtp_port');

                //Recipients
                $mail->setFrom(Helper::config('settings.smtp_email_address'), Helper::config('settings.name'));
                $mail->addAddress($email, $recipient);

                //Content
                $mail->isHTML(true);
                $mail->Subject = $title;
                $mail->Body = $body;
                $mail->AltBody = strip_tags($body);

                $mail->send();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return true;
    }

    public function getNotifications($userId, $limit = 20, $page = 1)
    {
        $notificationModel = new Notifications();
        $notifications = $notificationModel
            ->where('user_id', $userId)
            ->grouped(function ($q) {
                $q->where('status', 'active')
                    ->orWhere('status', 'viewed');
            })
            ->pagination($limit, $page)
            ->orderBy('created_at', 'DESC')
            ->getAll();

        return $notifications;
    }

    public function getUnreadNotificationCount($userId)
    {
        $notificationModel = new Notifications();
        $notifications = $notificationModel->count('id', 'total')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->get()->total;

        return (int)$notifications;
    }

    public function getNotificationList($userId, $page = 1, $mini = true, $withParent = true)
    {

        $limit = $mini ? 15 : 20;
        $return = '';

        $notifications = $this->getNotifications($userId, $limit, $page);
        // $notifications = [];

        if (!empty($notifications)) {
            $return = $withParent ? '<div class="list-group list-group-flush list-group-hoverable list-group-notification">' : '';
            foreach ($notifications as $notification) {

                $notification->details = json_decode($notification->details);
                $return .= '
                <div class="list-group-item notification-' . $notification->id . '">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="status-dot d-block' . ($notification->status === 'active' ? ' status-dot-animated bg-green' : '') . '"></span></div>
                        <div class="col' . ($mini ? '  text-truncate' : '') . '">
                            <a href="javascript:;" ' . ($notification->status === 'active' ? 'data-kx-action="' . Helper::base('auth/notifications/view/' . $notification->id) . '" ' : '') . 'class="text-body d-inline-block">
                                ' . Helper::lang($notification->details->title) . '
                            </a>
                            <time class="ms-2 timeago badge badge-outline text-blue" datetime="' . date('c', (int)$notification->created_at) . '">' . date('d.m H:i', (int)$notification->created_at) . '</time>
                            <div class="d-block text-secondary mt-n1' . ($mini ? '  text-truncate' : '') . '" title="' . Helper::lang($notification->details->body) . '">
                                ' . Helper::lang($notification->details->body) . '
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="javascript:;" data-kx-again data-kx-action="' . Helper::base('auth/notifications/delete/' . $notification->id) . '" class="list-group-item-actions">
                                <i class="ti ti-trash icon"></i>
                            </a>
                        </div>
                    </div>
                </div>';
            }
            $return .= $withParent ? '</div>' : '';
        } else {
            $return = '
            <div class="card-body">
                <p class="text-center text-muted h-100 d-flex align-items-center justify-content-center">' . Helper::lang('base.no_notifications') . '</p>
            </div>';
        }

        return [
            'list' => $return,
            'notifications' => $notifications
        ];
    }

    public function getNotification($id)
    {
        $notificationModel = new Notifications();
        $notification = $notificationModel
            ->where('id', $id)
            ->get();

        return $notification;
    }

    public function updateNotification($id, $data)
    {
        $notificationModel = new Notifications();
        $notification = $notificationModel
            ->where('id', $id)
            ->update($data);

        return $notification;
    }
}
