<?php

declare(strict_types = 1);

namespace Jellyfish\Kernel;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class KernelServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $pimple['key'] = 'value';
    }
}
