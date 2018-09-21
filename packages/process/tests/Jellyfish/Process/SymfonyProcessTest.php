<?php

namespace Jellyfish\Process;

use Codeception\Test\Unit;

class SymfonyProcessTest extends Unit
{
    /**
     * @var \Jellyfish\Process\ProcessInterface
     */
    protected $symfonyProcess;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->symfonyProcess = new SymfonyProcess(['sleep', '5']);
    }

    public function testStartAndIsRunning(): void
    {
        $this->symfonyProcess->start();
        $this->assertTrue($this->symfonyProcess->isRunning());
    }
}
