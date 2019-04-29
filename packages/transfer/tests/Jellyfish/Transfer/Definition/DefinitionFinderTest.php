<?php

namespace Jellyfish\Transfer\Definition;

use Codeception\Test\Unit;
use Iterator;
use Jellyfish\Finder\FinderFactoryInterface;
use Jellyfish\Finder\FinderInterface;

class DefinitionFinderTest extends Unit
{
    /**
     * @var \Jellyfish\Transfer\Definition\DefinitionFinder
     */
    protected $definitionFinder;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var \Jellyfish\Finder\FinderFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $finderFactoryMock;

    /**
     * @var \Jellyfish\Finder\FinderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $finderMock;

    /**
     * @var \Iterator|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $iteratorMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->finderFactoryMock = $this->getMockBuilder(FinderFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->finderMock = $this->getMockBuilder(FinderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->iteratorMock = $this->getMockBuilder(Iterator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->rootDir = '/';

        $this->definitionFinder = new DefinitionFinder($this->finderFactoryMock, $this->rootDir);
    }

    /**
     * @return void
     */
    public function testFind(): void
    {
        $this->finderFactoryMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->finderMock);

        $this->finderMock->expects($this->atLeastOnce())
            ->method('in')
            ->with('{,vendor/*/*/}src/*/*/Transfer/')
            ->willReturn($this->finderMock);

        $this->finderMock->expects($this->atLeastOnce())
            ->method('name')
            ->with('*.transfer.json')
            ->willReturn($this->finderMock);

        $this->finderMock->expects($this->atLeastOnce())
            ->method('getIterator')
            ->willReturn($this->iteratorMock);

        $iterator = $this->definitionFinder->find();

        $this->assertEquals($this->iteratorMock, $iterator);
    }
}
