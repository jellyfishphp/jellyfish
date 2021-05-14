<?php

declare(strict_types=1);

namespace Jellyfish\Console;

use Codeception\Test\Unit;

class ConsoleFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Console\ConsoleFactory
     */
    protected ConsoleFactory $consoleFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->consoleFactory = new ConsoleFactory();
    }

    /**
     * @return void
     */
    public function testGetCommandList(): void
    {
        static::assertEmpty($this->consoleFactory->getCommandList());
    }
}
