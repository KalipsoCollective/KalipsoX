<?php

/**
 * @package KX
 * @subpackage Model\Logs
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class EmailLogs extends Model {

    function __construct () {

        $this->table = 'email_logs';

        parent::__construct();

    }
}