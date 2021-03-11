<?php

declare(strict_types=1);

namespace Jellyfish\LogMonolog;

use Jellyfish\Config\ConfigConstants;
use Jellyfish\Log\LogConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LogMonologServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerLogFacade($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\LogMonolog\LogMonologServiceProvider
     */
    protected function registerLogFacade(Container $container): LogMonologServiceProvider
    {
        $container->offsetSet(LogConstants::FACADE, static function (Container $container) {
            $logFactory = new LogMonologFactory(
                $container->offsetGet(ConfigConstants::FACADE),
                $container->offsetGet('root_dir')
            );

            return new LogMonologFacade($logFactory);
        });

        return $this;
    }
}
