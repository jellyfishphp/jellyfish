<?php

namespace Jellyfish\LockSymfony;

use Codeception\Test\Unit;
use Pimple\Container;
use Predis\Client;

class LockSymfonyServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container;
     */
    protected $container;

    /**
     * @var \Jellyfish\LockSymfony\LockSymfonyServiceProvider
     */
    protected $lockSymfonyServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $self = $this;

        $this->container = new Container();

        $this->container->offsetSet('redis_client', function () use ($self) {
            return $self->getMockBuilder(Client::class)
                ->disableOriginalConstructor()
                ->getMock();
        });

        $this->lockSymfonyServiceProvider = new LockSymfonyServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->lockSymfonyServiceProvider->register($this->container);

        $this->assertTrue($this->container->offsetExists('lock_factory'));
        $this->assertInstanceOf(LockFactory::class, $this->container->offsetGet('lock_factory'));
    }
}
