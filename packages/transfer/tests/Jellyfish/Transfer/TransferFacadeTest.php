<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use Codeception\Test\Unit;

class TransferFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\Transfer\TransferFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $transferFactoryMock;

    /**
     * @var \Jellyfish\Transfer\TransferGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $transferGeneratorMock;

    /**
     * @var \Jellyfish\Transfer\TransferCleanerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $transferCleanerMock;

    /**
     * @var \Jellyfish\Transfer\TransferFacade
     */
    protected TransferFacade $transferFacade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->transferFactoryMock = $this->getMockBuilder(TransferFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transferGeneratorMock = $this->getMockBuilder(TransferGeneratorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transferCleanerMock = $this->getMockBuilder(TransferCleanerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transferFacade = new TransferFacade($this->transferFactoryMock);
    }

    /**
     * @return void
     */
    public function testGenerate(): void
    {
        $this->transferFactoryMock->expects(static::atLeastOnce())
            ->method('getTransferGenerator')
            ->willReturn($this->transferGeneratorMock);

        $this->transferGeneratorMock->expects(static::atLeastOnce())
            ->method('generate')
            ->willReturn($this->transferGeneratorMock);

        static::assertEquals($this->transferFacade, $this->transferFacade->generate());
    }

    /**
     * @return void
     */
    public function testClean(): void
    {
        $this->transferFactoryMock->expects(static::atLeastOnce())
            ->method('getTransferCleaner')
            ->willReturn($this->transferCleanerMock);

        $this->transferCleanerMock->expects(static::atLeastOnce())
            ->method('clean')
            ->willReturn($this->transferCleanerMock);

        static::assertEquals($this->transferFacade, $this->transferFacade->clean());
    }
}
