<?php

namespace Jellyfish\Queue;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QueueServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple A container instance
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $self = $this;
    }
}
