<?php

/**
 * @package KX
 * @subpackage Model\Forms
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;

final class Forms extends Model
{

  function __construct()
  {

    $this->table = 'forms';
    $this->created = true;
    $this->updated = true;

    parent::__construct();
  }
}
