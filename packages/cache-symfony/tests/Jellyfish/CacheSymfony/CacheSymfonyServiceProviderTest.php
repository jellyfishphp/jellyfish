<?php

declare(strict_types=1);

namespace Jellyfish\CacheSymfony;

use Codeception\Test\Unit;
use Jellyfish\Cache\CacheConstants;
use Jellyfish\Cache\CacheFacadeInterface;
use Jellyfish\Config\ConfigConstants;
use Jellyfish\Config\ConfigFacadeInterface;
use Pimple\Container;

class CacheSymfonyServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configFacadeMock;

    /**
     * @var \Jellyfish\CacheSymfony\CacheSymfonyServiceProvider
     */
    protected $cacheSymfonyServiceProvider;

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

        $this->cacheSymfonyServiceProvider = new CacheSymfonyServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void {
        $this->cacheSymfonyServiceProvider->register($this->container);

        static::assertTrue($this->container->offsetExists(CacheConstants::FACADE));
        static::assertInstanceOf(
            CacheFacadeInterface::class,
            $this->container->offsetGet(CacheConstants::FACADE)
        );
    }
}
