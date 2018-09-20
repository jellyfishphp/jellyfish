<?php

namespace Jellyfish\Process;

use Codeception\Test\Unit;

class SymfonyProcessFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Process\ProcessFactoryInterface
     */
    protected $symfonyProcessFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->symfonyProcessFactory = new SymfonyProcessFactory();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $symfonyProcess = $this->symfonyProcessFactory->create(['ls', '-la']);
        $this->assertInstanceOf(SymfonyProcess::class, $symfonyProcess);
    }
}
