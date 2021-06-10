<?php

declare(strict_types=1);

namespace Jellyfish\Console;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConsoleServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerConsoleFacade($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Console\ConsoleServiceProvider
     */
    protected function registerConsoleFacade(Container $container): ConsoleServiceProvider
    {
        $container->offsetSet(ConsoleConstants::FACADE, static fn() => new ConsoleFacade(new ConsoleFactory()));

        return $this;
    }
}
