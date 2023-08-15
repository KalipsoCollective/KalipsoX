<?php

/**
 * @package KX
 * @subpackage Model\Menus
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;

final class Menus extends Model
{

  function __construct()
  {

    $this->table = 'menus';
    $this->created = true;
    $this->updated = true;

    parent::__construct();
  }
}
