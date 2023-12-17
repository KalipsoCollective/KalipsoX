<?php

/**
 * @package KX
 * @subpackage Middleware\Test
 */

declare(strict_types=1);

namespace KX\Middleware;

use KX\Core\Helper;
use KX\Core\Middleware;
// use KX\Core\Model;


use KX\Core\Request;

final class Test extends Middleware
{

    public function run(Request $request)
    {
        return $this->next();
    }

    public function redirectTo(Request $request)
    {
        return $this->redirect('/', 301);
    }
}
