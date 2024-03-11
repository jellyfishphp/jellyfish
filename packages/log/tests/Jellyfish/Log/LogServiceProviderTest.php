<?php

declare(strict_types = 1);

namespace Jellyfish\Log;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigInterface;
use Jellyfish\Event\EventConstants;
use Monolog\Logger;
use PHPUnit\Framework\MockObject\MockObject;
use Pimple\Container;
use Psr\Log\LoggerInterface;

class LogServiceProviderTest extends Unit
{
    protected Container $container;

    protected MockObject&ConfigInterface $configMock;

    protected LogServiceProvider $logServiceProvider;

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

        $this->container->offsetSet('config', static fn (): MockObject&ConfigInterface => $self->configMock);

        $this->container->offsetSet(EventConstants::CONTAINER_KEY_DEFAULT_EVENT_ERROR_HANDLERS, static fn (): array => []);

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

        $this->assertInstanceOf(LoggerInterface::class, $this->container->offsetGet(LogConstants::CONTAINER_KEY_LOGGER));
    }
}
