<?php

/**
 * @package KX
 * @subpackage Model\Contents
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;

final class Contents extends Model
{

  function __construct()
  {

    $this->table = 'contents';
    $this->created = true;
    $this->updated = true;

    parent::__construct();
  }
}
