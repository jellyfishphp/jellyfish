<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Jellyfish\Config\ConfigConstants;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LogServiceProvider implements ServiceProviderInterface
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
     * @return \Jellyfish\Log\LogServiceProvider
     */
    protected function registerLogFacade(Container $container): LogServiceProvider
    {
        $self = $this;

        $container->offsetSet(LogConstants::FACADE, static function (Container $container) use ($self) {
            $logFactory = new LogFactory();

            return (new LogFacade($logFactory))
                ->addHandler($self->createStreamHandler($container))
                ->addHandler($self->createRotatingFileHandler($container));
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Monolog\Handler\AbstractProcessingHandler
     *
     * @throws \Exception
     */
    protected function createStreamHandler(Container $container): HandlerInterface
    {
        /** @var \Jellyfish\Config\ConfigFacadeInterface $configFacade */
        $configFacade = $container->offsetGet(ConfigConstants::FACADE);

        return new StreamHandler(
            'php://stdout',
            $configFacade->get(LogConstants::LOG_LEVEL, LogConstants::DEFAULT_LOG_LEVEL)
        );
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Monolog\Handler\AbstractProcessingHandler
     *
     * @throws \Exception
     */
    protected function createRotatingFileHandler(Container $container): HandlerInterface
    {
        /** @var \Jellyfish\Config\ConfigFacadeInterface $configFacade */
        $configFacade = $container->offsetGet(ConfigConstants::FACADE);

        $filename = $container->offsetGet('root_dir') . 'var/log/jellyfish.log';

        return new RotatingFileHandler(
            $filename,
            0,
            $configFacade->get(LogConstants::LOG_LEVEL, LogConstants::DEFAULT_LOG_LEVEL)
        );
    }
}
