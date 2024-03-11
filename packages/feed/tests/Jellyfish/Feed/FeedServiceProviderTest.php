<?php

declare(strict_types=1);

namespace Jellyfish\Feed;

use Codeception\Test\Unit;
use Jellyfish\Feed\Command\RunFeedReaderCommand;
use Pimple\Container;

class FeedServiceProviderTest extends Unit
{
    protected Container $container;

    protected FeedServiceProvider $feedServiceProvider;

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

        $this->feedServiceProvider = new FeedServiceProvider();
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        $this->feedServiceProvider->register($this->container);

        $commands = $this->container->offsetGet('commands');
        $this->assertCount(1, $commands);
        $this->assertInstanceOf(RunFeedReaderCommand::class, $commands[0]);

        $jobManager = $this->container->offsetGet('feed_reader_manager');
        $this->assertInstanceOf(FeedReaderManagerInterface::class, $jobManager);
    }
}
