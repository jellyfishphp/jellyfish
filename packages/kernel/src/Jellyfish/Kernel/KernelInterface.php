<?php

namespace Jellyfish\Kernel;

use Pimple\Container;

interface KernelInterface
{
    /**
     * @return Container
     */
    public function getContainer(): Container;
}
