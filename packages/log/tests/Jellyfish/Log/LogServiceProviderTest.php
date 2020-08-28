<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigInterface;
use Jellyfish\Event\EventServiceProvider;
use Monolog\Logger;
use Pimple\Container;
use Psr\Log\LoggerInterface;

class LogServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\ServiceProviderInterface
     */
    protected $logServiceProvider;

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Jellyfish\Config\ConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configMock;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $self = $this;

        $this->container->offsetSet('root_dir', DIRECTORY_SEPARATOR);

        $this->container->offsetSet('config', static function () use ($self) {
            return $self->configMock;
        });

        $this->container->offsetSet(EventServiceProvider::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS, static function () {
            return [];
        });

        $this->logServiceProvider = new LogServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->configMock->expects(self::atLeastOnce())
            ->method('get')
            ->with(LogConstants::LOG_LEVEL, (string) LogConstants::DEFAULT_LOG_LEVEL)
            ->willReturn((string) Logger::DEBUG);

        $this->logServiceProvider->register($this->container);

        self::assertInstanceOf(
            LoggerInterface::class,
            $this->container->offsetGet(LogServiceProvider::CONTAINER_KEY_LOGGER)
        );
    }
}
