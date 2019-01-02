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

        $tempDir = vfsStream::setup('tmp')->url();
        $tempDir = rtrim($tempDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->symfonyProcessFactory = new ProcessFactory($tempDir);
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
