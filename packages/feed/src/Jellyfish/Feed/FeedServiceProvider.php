<?php

declare(strict_types = 1);

namespace Jellyfish\Feed;

use Jellyfish\Feed\Command\RunFeedReaderCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @see \Jellyfish\Feed\FeedServiceProviderTest
 */
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
        $container->offsetSet('feed_reader_manager', static fn (): FeedReaderManager => new FeedReaderManager());

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\Feed\FeedServiceProvider
     */
    protected function registerCommands(Container $container): FeedServiceProvider
    {
        $container->extend('commands', static function (array $commands, Container $container): array {
            $commands[] = new RunFeedReaderCommand($container->offsetGet('feed_reader_manager'));
            return $commands;
        });

        return $this;
    }
}
