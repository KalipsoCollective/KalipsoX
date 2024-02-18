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
}
