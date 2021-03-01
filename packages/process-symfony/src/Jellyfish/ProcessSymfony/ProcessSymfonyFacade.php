<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Jellyfish\Process\ProcessFacadeInterface;
use Jellyfish\Process\ProcessInterface;

class ProcessSymfonyFacade implements ProcessFacadeInterface
{
    /**
     * @var \Jellyfish\ProcessSymfony\ProcessSymfonyFactory
     */
    protected $factory;

    /**
     * @param \Jellyfish\ProcessSymfony\ProcessSymfonyFactory $factory
     */
    public function __construct(ProcessSymfonyFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string[] $command
     *
     * @return \Jellyfish\Process\ProcessInterface
     */
    public function createProcess(array $command): ProcessInterface
    {
        return $this->factory->createProcess($command);
    }
}
