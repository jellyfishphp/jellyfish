<?php

declare(strict_types = 1);

namespace Jellyfish\ProcessSymfony;

use Codeception\Test\Unit;
use Pimple\Container;

class ProcessSymfonyServiceProviderTest extends Unit
{
    protected Container $container;

    protected ProcessSymfonyServiceProvider $processSymfonyServiceProvider;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->processSymfonyServiceProvider = new ProcessSymfonyServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->processSymfonyServiceProvider->register($this->container);

        $this->assertInstanceOf(ProcessFactory::class, $this->container->offsetGet('process_factory'));
    }
}
