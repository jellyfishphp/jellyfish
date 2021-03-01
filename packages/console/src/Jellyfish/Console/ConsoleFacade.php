<?php

declare(strict_types=1);

namespace Jellyfish\Console;

use Symfony\Component\Console\Command\Command;

class ConsoleFacade implements ConsoleFacadeInterface
{
    /**
     * @var \Jellyfish\Console\ConsoleFactory
     */
    protected $factory;

    /**
     * @param \Jellyfish\Console\ConsoleFactory $factory
     */
    public function __construct(ConsoleFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param \Symfony\Component\Console\Command\Command $command
     * @return \Jellyfish\Console\ConsoleFacadeInterface
     */
    public function addCommand(Command $command): ConsoleFacadeInterface
    {
        $this->factory->getCommandList()->append($command);

        return $this;
    }

    /**
     * @return \Symfony\Component\Console\Command\Command[]
     */
    public function getCommands(): array
    {
        return $this->factory->getCommandList()->getArrayCopy();
    }
}
