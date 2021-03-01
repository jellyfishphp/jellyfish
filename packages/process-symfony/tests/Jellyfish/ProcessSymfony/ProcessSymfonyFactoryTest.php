<?php

declare(strict_types=1);

namespace Jellyfish\ProcessSymfony;

use Codeception\Test\Unit;

class ProcessSymfonyFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\ProcessSymfony\ProcessSymfonyFactory
     */
    protected $processSymfonyFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();
        $this->processSymfonyFactory = new ProcessSymfonyFactory();
    }

    /**
     * @return void
     */
    public function testCreateProcess(): void
    {
        static::assertInstanceOf(Process::class, $this->processSymfonyFactory->createProcess(['ls', '-la']));
    }
}
