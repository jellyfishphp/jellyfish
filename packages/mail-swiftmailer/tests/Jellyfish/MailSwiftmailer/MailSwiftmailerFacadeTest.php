<?php

namespace Jellyfish\MailSwiftmailer;

use Codeception\Test\Unit;
use Generated\Transfer\Mail;

class MailSwiftmailerFacadeTest extends Unit
{
    /**
     * @var \Jellyfish\MailSwiftmailer\MailSwiftmailerFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $factoryMock;

    /**
     * @var \Jellyfish\MailSwiftmailer\MailHandlerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $mailHandlerMock;

    /**
     * @var \Generated\Transfer\Mail|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $mailMock;

    /**
     * @var \Jellyfish\MailSwiftmailer\MailSwiftmailerFacade
     */
    protected MailSwiftmailerFacade $facade;

    /**
     * @Override
     */
    protected function _before(): void
    {
        parent::_before();

        $this->factoryMock = $this->getMockBuilder(MailSwiftmailerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mailHandlerMock = $this->getMockBuilder(MailHandlerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mailMock = $this->getMockBuilder(Mail::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->facade = new MailSwiftmailerFacade($this->factoryMock);
    }

    /**
     * @return void
     */
    public function testSend(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createMailHandler')
            ->willReturn($this->mailHandlerMock);

        $this->mailHandlerMock->expects(static::atLeastOnce())
            ->method('send')
            ->with($this->mailMock)
            ->willReturn($this->mailHandlerMock);

        static::assertEquals(
            $this->facade,
            $this->facade->send($this->mailMock)
        );
    }
}
