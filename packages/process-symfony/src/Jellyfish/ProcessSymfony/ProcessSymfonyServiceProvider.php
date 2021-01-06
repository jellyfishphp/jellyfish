<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Jellyfish\Process\ProcessConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ProcessSymfonyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerProcessFacade($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\ProcessSymfony\ProcessSymfonyServiceProvider
     */
    protected function registerProcessFacade(Container $container): ProcessSymfonyServiceProvider
    {
        $container->offsetSet(ProcessConstants::FACADE, static function () {
            return new ProcessSymfonyFacade(new ProcessSymfonyFactory());
        });

        return $this;
    }
}
