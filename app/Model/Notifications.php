<?php

/**
 * @package KX
 * @subpackage Model\Notifications
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;

final class Notifications extends Model {

    function __construct () {

        $this->table = 'notifications';

        parent::__construct();

    }
}