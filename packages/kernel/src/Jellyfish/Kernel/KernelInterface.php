<?php

namespace Jellyfish\Kernel;

use Pimple\Container;

interface KernelInterface
{
    /**
     * @return \Pimple\Container
     */
    public function getContainer(): Container;
}
