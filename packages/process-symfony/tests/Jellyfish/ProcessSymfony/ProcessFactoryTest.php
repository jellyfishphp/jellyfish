<?php

namespace Jellyfish\ProcessSymfony;

use Codeception\Test\Unit;
use org\bovigo\vfs\vfsStream;

class ProcessFactoryTest extends Unit
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
        $this->symfonyProcessFactory = new ProcessFactory();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $symfonyProcess = $this->symfonyProcessFactory->create(['ls', '-la']);
        $this->assertInstanceOf(Process::class, $symfonyProcess);
    }
}
