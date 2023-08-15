<?php

/**
 * @package KX
 * @subpackage Model\Logs
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class Logs extends Model {

    function __construct () {

        $this->table = 'logs';
        $this->created = true;

        parent::__construct();

    }
}