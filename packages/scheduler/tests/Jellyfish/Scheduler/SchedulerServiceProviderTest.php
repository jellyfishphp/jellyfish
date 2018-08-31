<?php

namespace Jellyfish\Scheduler;

use Codeception\Test\Unit;
use Jellyfish\Scheduler\Command\RunCommand;
use Pimple\Container;

class SchedulerServiceProviderTest extends Unit
{
    /**
     * @var \Pimple\Container;
     */
    protected $container;

    /**
     * @var \Jellyfish\Scheduler\SchedulerServiceProvider
     */
    protected $schedulerServiceProvider;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();

        $this->container->offsetSet('commands', function ($container) {
            return [];
        });

        $this->schedulerServiceProvider = new SchedulerServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->schedulerServiceProvider->register($this->container);

        $commands = $this->container->offsetGet('commands');
        $this->assertCount(1, $commands);
        $this->assertInstanceOf(RunCommand::class, $commands[0]);

        $scheduler = $this->container->offsetGet('scheduler');
        $this->assertInstanceOf(SchedulerInterface::class, $scheduler);
    }
}
