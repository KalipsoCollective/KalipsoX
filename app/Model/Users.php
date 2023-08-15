<?php

/**
 * @package KX
 * @subpackage Model\Users
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;

final class Users extends Model {

    function __construct () {

        $this->table = 'users';
        $this->created = true;
        $this->updated = true;

        parent::__construct();

    }
}