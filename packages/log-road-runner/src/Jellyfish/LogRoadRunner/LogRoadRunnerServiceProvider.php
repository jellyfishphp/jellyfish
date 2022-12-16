<?php

declare(strict_types=1);

namespace Jellyfish\LogRoadRunner;

use Jellyfish\Config\ConfigConstants;
use Jellyfish\Log\LogConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LogRoadRunnerServiceProvider implements ServiceProviderInterface
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
     * @return \Jellyfish\LogRoadRunner\LogRoadRunnerServiceProvider
     */
    protected function registerLogFacade(Container $container): LogRoadRunnerServiceProvider
    {
        $container->offsetSet(LogConstants::FACADE, static function (Container $container) {
            $logFactory = new LogRoadRunnerFactory(
                $container->offsetGet(ConfigConstants::FACADE),
                $container->offsetGet('root_dir')
            );

            return new \Jellyfish\LogRoadRunner\LogRoadRunnerFacade($logFactory);
        });

        return $this;
    }
}
