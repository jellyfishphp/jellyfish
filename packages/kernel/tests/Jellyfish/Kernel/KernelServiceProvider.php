<?php

declare(strict_types=1);

namespace Jellyfish\Kernel;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class KernelServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['key'] = 'value';
    }
}
