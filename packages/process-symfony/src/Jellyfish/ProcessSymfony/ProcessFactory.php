<?php

declare(strict_types = 1);

namespace Jellyfish\ProcessSymfony;

use Jellyfish\Process\ProcessFactoryInterface;
use Jellyfish\Process\ProcessInterface;

/**
 * @see \Jellyfish\ProcessSymfony\ProcessFactoryTest
 */
class ProcessFactory implements ProcessFactoryInterface
{
    /**
     * @param array $command
     *
     * @return \Jellyfish\Process\ProcessInterface
     */
    public function create(array $command): ProcessInterface
    {
        return new Process($command);
    }
}
