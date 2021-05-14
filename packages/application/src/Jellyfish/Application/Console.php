<?php

declare(strict_types=1);

namespace Jellyfish\Application;

use Jellyfish\Console\ConsoleConstants;
use Jellyfish\Kernel\KernelInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;

use function count;

class Console extends BaseApplication
{
    /**
     * @var \Jellyfish\Kernel\KernelInterface
     */
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

        $consoleFacade = $this->kernel->getContainer()->offsetGet(ConsoleConstants::FACADE);
        $commandsToAdd = $consoleFacade->getCommands();

        if (count($commandsToAdd) === 0) {
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
