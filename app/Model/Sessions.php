<?php

/**
 * @package KX
 * @subpackage Model\Sessions
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;

final class Sessions extends Model {

    function __construct () {

        $this->table = 'sessions';

        parent::__construct();

    }
}