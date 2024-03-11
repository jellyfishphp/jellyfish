<?php

declare(strict_types=1);

namespace Jellyfish\Application;

use Jellyfish\Kernel\KernelInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;

use function count;
use function is_array;

/**
 * @see \Jellyfish\Application\ConsoleTest
 */
class Console extends BaseApplication
{
    protected KernelInterface $kernel;

    /**
     * @param \Jellyfish\Kernel\KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct('Jellyfish', '1.0.0');

        $this->kernel = $kernel;
    }

    /**
     * @return \Symfony\Component\Console\Command\Command[]
     */
    protected function getDefaultCommands(): array
    {
        $defaultCommands = parent::getDefaultCommands();

        if (!$this->kernel->getContainer()->offsetExists('commands')) {
            return $defaultCommands;
        }

        $commandsToAdd = $this->kernel->getContainer()->offsetGet('commands');

        if (!is_array($commandsToAdd) || count($commandsToAdd) === 0) {
            return $defaultCommands;
        }

        foreach ($commandsToAdd as $command) {
            if (!$command instanceof Command) {
                continue;
            }

            $defaultCommands[] = $command;
        }

        return $defaultCommands;
    }
}
