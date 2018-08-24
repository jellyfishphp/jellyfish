<?php

namespace Jellyfish\Log;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigInterface;
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

        $this->container['config'] = function ($container) use ($self) {
            return $self->configMock;
        };

        $this->logServiceProvider = new LogServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->configMock->expects($this->atLeastOnce())
            ->method('get')
            ->with(LogConstants::LOG_LEVEL, LogConstants::DEFAULT_LOG_LEVEL)
            ->willReturn(Logger::DEBUG);

        $this->logServiceProvider->register($this->container);

        $this->assertInstanceOf(LoggerInterface::class, $this->container['logger']);
    }
}
