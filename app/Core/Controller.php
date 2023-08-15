<?php

/**
 * @package KX
 * @subpackage Core\Controller
 */

declare(strict_types=1);

namespace KX\Core;

class Controller
{

    public $container = null;

    /**
     *  @param object container  factory class   
     *  @return void
     **/

    public function __construct($container = null)
    {

        if ($container) {
            $this->container = $container;
        }
    }

    public function get($key = null)
    {

        return is_null($key) ?
            $this->container : (isset($this->container->{$key}) ?
                $this->container->{$key} :
                null
            );
    }
}
