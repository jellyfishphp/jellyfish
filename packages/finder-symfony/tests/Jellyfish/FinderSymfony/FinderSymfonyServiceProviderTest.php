<?php

namespace Jellyfish\FinderSymfony;

use Codeception\Test\Unit;
use Pimple\Container;

class FinderSymfonyServiceProviderTest extends Unit
{
    /**
     * @var \Jellyfish\FinderSymfony\FinderSymfonyServiceProvider
     */
    protected $finderSymfonyServiceProvider;

    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->finderSymfonyServiceProvider = new FinderSymfonyServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->finderSymfonyServiceProvider->register($this->container);

        $this->assertInstanceOf(FinderFactory::class, $this->container->offsetGet('finder_factory'));
    }
}
