<?php

/**
 * @package KX
 * @subpackage Model\UserRoles
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;

final class UserRoles extends Model {

    function __construct () {

        $this->table = 'user_roles';
        $this->created = true;
        $this->updated = true;

        parent::__construct();

    }
}