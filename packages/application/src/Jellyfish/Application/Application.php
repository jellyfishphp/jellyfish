<?php

namespace Jellyfish\Application;

use Jellyfish\Kernel\KernelInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;

class Application extends BaseApplication
{
    /**
     * @var \Jellyfish\Kernel\KernelInterface
     */
    protected $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct('Jellyfish', '1.0.0');

        $this->kernel = $kernel;
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return Command[] An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        if (!$this->kernel->getContainer()->offsetExists('commands')) {
            return $defaultCommands;
        }

        $commandsToAdd = $this->kernel->getContainer()->offsetGet('commands');

        if (!\is_array($commandsToAdd) || \count($commandsToAdd) === 0) {
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
