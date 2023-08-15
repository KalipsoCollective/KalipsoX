<?php

/**
 * @package KX
 * @subpackage Model\EmailLogs
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;

final class EmailLogs extends Model {

    function __construct () {

        $this->table = 'email_logs';

        parent::__construct();

    }
}