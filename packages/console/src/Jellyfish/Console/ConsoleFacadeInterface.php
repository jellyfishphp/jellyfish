<?php

declare(strict_types=1);

namespace Jellyfish\Console;

use Symfony\Component\Console\Command\Command;

interface ConsoleFacadeInterface
{
    /**
     * @param \Symfony\Component\Console\Command\Command $command
     *
     * @return \Jellyfish\Console\ConsoleFacadeInterface
     */
    public function addCommand(Command $command): ConsoleFacadeInterface;

    /**
     * @return \Symfony\Component\Console\Command\Command[]
     */
    public function getCommands(): array;
}
