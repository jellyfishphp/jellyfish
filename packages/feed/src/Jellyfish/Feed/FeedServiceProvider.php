<?php

namespace Jellyfish\Feed;

use Jellyfish\Feed\Command\RunFeedReaderCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class FeedServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     */
    public function register(Container $pimple)
    {
        $self = $this;

        $pimple->offsetSet('feed_reader_manager', function () use ($self) {
            return $self->createFeedReaderManager();
        });

        $pimple->extend('commands', function (array $commands, Container $container) use ($self) {
            $commands[] = $self->createRunFeedReaderCommand($container->offsetGet('feed_reader_manager'));

            return $commands;
        });
    }

    /**
     * @return \Jellyfish\Feed\FeedReaderManagerInterface
     */
    protected function createFeedReaderManager(): FeedReaderManagerInterface
    {
        return new FeedReaderManager();
    }

    /**
     * @param \Jellyfish\Feed\FeedReaderManagerInterface $feedReaderManager
     *
     * @return \Jellyfish\Feed\Command\RunFeedReaderCommand
     */
    protected function createRunFeedReaderCommand(FeedReaderManagerInterface $feedReaderManager): RunFeedReaderCommand
    {
        return new RunFeedReaderCommand($feedReaderManager);
    }
}
