<?php

namespace Jellyfish\Feed;

interface FeedReaderInterface
{
    /**
     * @return string
     */
    public function read(): string;
}
