<?php

declare(strict_types=1);

namespace Jellyfish\Kernel;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class KernelServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     */
    public function register(Container $container): void
    {
        $container['key'] = 'value';
    }
}
