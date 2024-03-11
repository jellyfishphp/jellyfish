<?php

declare(strict_types = 1);

namespace Jellyfish\Feed;

interface FeedReaderManagerInterface
{
    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function existsFeedReader(string $identifier): bool;

    /**
     * @param string $identifier
     * @param \Jellyfish\Feed\FeedReaderInterface $feedReader
     *
     * @return \Jellyfish\Feed\FeedReaderManagerInterface
     */
    public function setFeedReader(string $identifier, FeedReaderInterface $feedReader): FeedReaderManagerInterface;

    /**
     * @param string $identifier
     *
     * @return \Jellyfish\Feed\FeedReaderManagerInterface
     */
    public function unsetFeedReader(string $identifier): FeedReaderManagerInterface;

    /**
     * @param string $identifier
     *
     * @return \Jellyfish\Feed\FeedReaderInterface
     */
    public function getFeederReader(string $identifier): FeedReaderInterface;

    /**
     * @param string $identifier
     *
     * @return string
     */
    public function readFromFeedReader(string $identifier): string;
}
