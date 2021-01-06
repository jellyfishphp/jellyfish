<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Codeception\Test\Unit;
use Jellyfish\Process\ProcessConstants;
use Pimple\Container;

class ProcessSymfonyServiceProviderTest extends Unit
{
    /**
     * @var \Jellyfish\ProcessSymfony\ProcessSymfonyServiceProvider
     */
    protected $processSymfonyServiceProvider;

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

        $this->processSymfonyServiceProvider = new ProcessSymfonyServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->processSymfonyServiceProvider->register($this->container);

        static::assertInstanceOf(
            ProcessSymfonyFacade::class,
            $this->container->offsetGet(ProcessConstants::FACADE)
        );
    }
}
