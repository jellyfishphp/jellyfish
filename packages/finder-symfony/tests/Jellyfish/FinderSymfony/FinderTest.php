<?php

declare(strict_types=1);

namespace Jellyfish\FinderSymfony;

use Codeception\Test\Unit;
use Iterator;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder as SymfonyFinder;

class FinderTest extends Unit
{
    /**
     * @var \Symfony\Component\Finder\Finder|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $symfonyFinderMock;

    /**
     * @var \Iterator|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $iteratorMock;

    /**
     * @var \Jellyfish\Finder\FinderInterface
     */
    protected $finder;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->symfonyFinderMock = $this->getMockBuilder(SymfonyFinder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->iteratorMock = $this->getMockBuilder(Iterator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->finder = new Finder($this->symfonyFinderMock);
    }

    /**
     * @return void
     */
    public function testIn(): void
    {
        $directories = [
            '/usr/local/apache2/htdocs/',
        ];

        $this->symfonyFinderMock->expects(static::atLeastOnce())
            ->method('in')
            ->with($directories[0])
            ->willReturn($this->symfonyFinderMock);

        static::assertEquals($this->finder, $this->finder->in($directories));
    }

    /**
     * @return void
     */
    public function testInWithNonExistingDirectory(): void
    {
        $directories = [
            '/usr/local/apache2/htdocs/',
        ];

        $this->symfonyFinderMock->expects(static::atLeastOnce())
            ->method('in')
            ->with($directories[0])
            ->willThrowException(new DirectoryNotFoundException());

        static::assertEquals($this->finder, $this->finder->in($directories));
    }

    /**
     * @return void
     */
    public function testName(): void
    {
        $pattern = '*.html';

        $this->symfonyFinderMock->expects(static::atLeastOnce())
            ->method('name')
            ->with($pattern)
            ->willReturn($this->symfonyFinderMock);

        static::assertEquals($this->finder, $this->finder->name($pattern));
    }

    /**
     * @return void
     */
    public function testDepth(): void
    {
        $level = 0;

        $this->symfonyFinderMock->expects(static::atLeastOnce())
            ->method('depth')
            ->with($level)
            ->willReturn($this->symfonyFinderMock);

        static::assertEquals($this->finder, $this->finder->depth($level));
    }

    /**
     * @return void
     */
    public function testGetIterator(): void
    {
        $this->symfonyFinderMock->expects(static::atLeastOnce())
            ->method('getIterator')
            ->willReturn($this->iteratorMock);

        static::assertEquals($this->iteratorMock, $this->finder->getIterator());
    }
}
