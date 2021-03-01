<?php

declare(strict_types=1);

namespace Jellyfish\LockSymfony;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigConstants;
use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Lock\LockConstants;
use Jellyfish\Lock\LockFacadeInterface;
use Pimple\Container;

class LockSymfonyServiceProviderTest extends Unit
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configFacadeMock;

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Jellyfish\LockSymfony\LockSymfonyServiceProvider
     */
    protected $lockSymfonyServiceProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->configFacadeMock = $this->getMockBuilder(ConfigFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $self = $this;

        $this->container->offsetSet(ConfigConstants::FACADE, static function () use ($self) {
            return $self->configFacadeMock;
        });

        $this->lockSymfonyServiceProvider = new LockSymfonyServiceProvider();
    }

    public function testRegister(): void
    {
        $this->lockSymfonyServiceProvider->register($this->container);

        self::assertTrue($this->container->offsetExists(LockConstants::FACADE));
        self::assertInstanceOf(
            LockFacadeInterface::class,
            $this->container->offsetGet(LockConstants::FACADE)
        );
    }
}
