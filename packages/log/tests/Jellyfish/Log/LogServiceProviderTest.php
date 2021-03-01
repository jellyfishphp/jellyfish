<?php

declare(strict_types=1);

namespace Jellyfish\Log;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigConstants;
use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Event\EventConstants;
use Pimple\Container;

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
     * @var \Jellyfish\Config\ConfigFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configFacadeMock;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->configFacadeMock = $this->getMockBuilder(ConfigFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $self = $this;

        $this->container->offsetSet('root_dir', DIRECTORY_SEPARATOR);

        $this->container->offsetSet(ConfigConstants::FACADE, static function () use ($self) {
            return $self->configFacadeMock;
        });

        $this->logServiceProvider = new LogServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->configFacadeMock->expects(self::atLeastOnce())
            ->method('get')
            ->with(LogConstants::LOG_LEVEL, LogConstants::DEFAULT_LOG_LEVEL)
            ->willReturn(LogConstants::LOG_LEVEL_DEBUG);

        $this->logServiceProvider->register($this->container);

        self::assertInstanceOf(
            LogFacadeInterface::class,
            $this->container->offsetGet(LogConstants::FACADE)
        );
    }
}
