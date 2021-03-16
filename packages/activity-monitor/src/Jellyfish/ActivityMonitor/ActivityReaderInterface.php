<?php

namespace Jellyfish\ActivityMonitor;

interface ActivityReaderInterface
{
    /**
     * @param int $id
     *
     * @return \Jellyfish\ActivityMonitor\ActivityInterface|null
     */
    public function getById(int $id): ?ActivityInterface;

    /**
     * @return \Jellyfish\ActivityMonitor\ActivityInterface[]
     *
     * @throws \Jellyfish\Process\Exception\AlreadyStartedException
     * @throws \Jellyfish\Process\Exception\NotStartedException
     * @throws \Jellyfish\Process\Exception\NotTerminatedException
     */
    public function getAll(): array;
}
