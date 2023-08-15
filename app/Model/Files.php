<?php

/**
 * @package KX
 * @subpackage Model\Files
 */

declare(strict_types=1);

namespace KX\Model;

use KX\Core\Model;

final class Files extends Model
{

  function __construct()
  {

    $this->table = 'files';
    $this->created = true;
    $this->updated = true;

    parent::__construct();
  }
}
