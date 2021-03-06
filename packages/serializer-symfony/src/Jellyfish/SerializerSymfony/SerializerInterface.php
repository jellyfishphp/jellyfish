<?php

declare(strict_types=1);

namespace Jellyfish\SerializerSymfony;

interface SerializerInterface
{
    /**
     * @param object $data
     * @param string $format
     *
     * @return string
     */
    public function serialize(object $data, string $format): string;

    /**
     * @param string $data
     * @param string $type
     * @param string $format
     *
     * @return object
     */
    public function deserialize(string $data, string $type, string $format): object;
}
