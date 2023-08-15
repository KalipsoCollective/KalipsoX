<?php

/**
 * @package KX
 * @subpackage Model\Sessions
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;
use KX\Core\Helper;

final class Sessions extends Model {

    function __construct () {

        $this->table = 'sessions';

        parent::__construct();

    }
}