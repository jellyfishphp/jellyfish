<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @see \Jellyfish\ProcessSymfony\ProcessSymfonyServiceProviderTest
 */
class ProcessSymfonyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerProcessFactory($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\ProcessSymfony\ProcessSymfonyServiceProvider
     */
    protected function registerProcessFactory(Container $container): ProcessSymfonyServiceProvider
    {
        $container->offsetSet('process_factory', static fn(): ProcessFactory => new ProcessFactory());

        return $this;
    }
}
