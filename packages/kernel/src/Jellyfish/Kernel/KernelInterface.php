<?php

declare(strict_types=1);

namespace Jellyfish\Kernel;

use Pimple\Container;

interface KernelInterface
{
    /**
     * @return \Pimple\Container
     */
    public function getContainer(): Container;
}
