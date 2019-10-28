<?php

declare(strict_types=1);

namespace Jellyfish\Feed;

use Jellyfish\Feed\Command\RunFeedReaderCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class FeedServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @®return void
     */
    public function register(Container $pimple): void
    {
        $this->registerFeedReaderManager($pimple)
            ->registerCommands($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Feed\FeedServiceProvider
     */
    protected function registerFeedReaderManager(Container $container): FeedServiceProvider
    {
        $container->offsetSet('feed_reader_manager', function () {
            return new FeedReaderManager();
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Feed\FeedServiceProvider
     */
    protected function registerCommands(Container $container): FeedServiceProvider
    {
        $container->extend('commands', function (array $commands, Container $container) {
            $commands[] = new RunFeedReaderCommand($container->offsetGet('feed_reader_manager'));

            return $commands;
        });

        return $this;
    }
}
