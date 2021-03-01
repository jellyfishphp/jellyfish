<?php

declare(strict_types=1);

namespace Jellyfish\FinderSymfony;

use Codeception\Test\Unit;
use Jellyfish\Finder\FinderInterface;

class FinderSymfonyFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\FinderSymfony\FinderSymfonyFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $finderSymfonyFactoryMock;

    /**
     * @var \Jellyfish\Finder\FinderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $finderMock;

    /**
     * @var \Jellyfish\FinderSymfony\FinderSymfonyFacade
     */
    protected $finderSymfonyFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->finderSymfonyFactoryMock = $this->getMockBuilder(FinderSymfonyFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->finderMock = $this->getMockBuilder(FinderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->finderSymfonyFacade = new FinderSymfonyFacade(
            $this->finderSymfonyFactoryMock
        );
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $this->finderSymfonyFactoryMock->expects(static::atLeastOnce())
            ->method('createFinder')
            ->willReturn($this->finderMock);

        static::assertEquals($this->finderMock, $this->finderSymfonyFacade->createFinder());
    }
}
