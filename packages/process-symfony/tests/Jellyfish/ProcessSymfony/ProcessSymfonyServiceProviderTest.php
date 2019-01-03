<?php

namespace Jellyfish\ProcessSymfony;

use Codeception\Test\Unit;
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

        $this->container->offsetSet('root_dir', function () {
            return DIRECTORY_SEPARATOR;
        });

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