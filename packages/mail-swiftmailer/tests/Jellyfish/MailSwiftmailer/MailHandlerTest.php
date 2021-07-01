<?php

namespace Jellyfish\MailSwiftmailer;

use Codeception\Test\Unit;
use Generated\Transfer\Mail;
use Generated\Transfer\Mail\Attachment;
use Generated\Transfer\Mail\Body;
use Generated\Transfer\Mail\Contact;
use Swift_Attachment as SwiftAttachment;
use Swift_Mailer as SwiftMailer;
use Swift_Message as SwiftMessage;

class MailHandlerTest extends Unit
{
    /**
     * @var \Swift_Mailer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $swiftMailerMock;

    /**
     * @var \Swift_Message|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $swiftMessageMock;

    /**
     * @var \Jellyfish\MailSwiftmailer\SwiftAttachmentFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $swiftAttachmentFactoryMock;

    /**
     * @var \Swift_Attachment|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $swiftAttachmentMock;

    /**
     * @var \Generated\Transfer\Mail|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $mailMock;

    /**
     * @var \Generated\Transfer\Mail\Contact|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $fromMock;

    /**
     * @var \Generated\Transfer\Mail\Contact|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $toMock;

    /**
     * @var \Generated\Transfer\Mail\Contact|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $ccMock;

    /**
     * @var \Generated\Transfer\Mail\Contact|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $bccMock;

    /**
     * @var \Generated\Transfer\Mail\Body|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $bodyMock;

    /**
     * @var \Generated\Transfer\Mail\Attachment|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $attachmentMock;

    /**
     * @var \Jellyfish\MailSwiftmailer\MailHandler
     */
    protected MailHandler $mailHandler;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->swiftMailerMock = $this->getMockBuilder(SwiftMailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->swiftMessageMock = $this->getMockBuilder(SwiftMessage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->swiftAttachmentFactoryMock = $this->getMockBuilder(SwiftAttachmentFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->swiftAttachmentMock = $this->getMockBuilder(SwiftAttachment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mailMock = $this->getMockBuilder(Mail::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fromMock = $this->getMockBuilder(Contact::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->toMock = $this->getMockBuilder(Contact::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->ccMock = $this->getMockBuilder(Contact::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->bccMock = $this->getMockBuilder(Contact::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->bodyMock = $this->getMockBuilder(Body::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->attachmentMock = $this->getMockBuilder(Attachment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mailHandler = new MailHandler(
            $this->swiftMailerMock,
            $this->swiftMessageMock,
            $this->swiftAttachmentFactoryMock
        );
    }

    /**
     * @return void
     */
    public function testSend(): void
    {
        $from = ['John Doe', 'john.doe@example.com'];
        $to = ['Max Doe', 'max.doe@example.com'];
        $cc = ['Bob Doe', 'bob.doe@example.com'];
        $bcc = ['Chris Doe', 'chris.doe@example.com'];

        $subject = 'Subject';
        $body = ['Body', '<p>Body</p>'];

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getFrom')
            ->willReturn($this->fromMock);

        $this->fromMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($from[0]);

        $this->fromMock->expects(static::atLeastOnce())
            ->method('getEmail')
            ->willReturn($from[1]);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('setFrom')
            ->with($from[1], $from[0])
            ->willReturn($this->swiftMessageMock);

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getToList')
            ->willReturn([$this->toMock]);

        $this->toMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($to[0]);

        $this->toMock->expects(static::atLeastOnce())
            ->method('getEmail')
            ->willReturn($to[1]);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('addTo')
            ->with($to[1], $to[0])
            ->willReturn($this->swiftMessageMock);

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getBccList')
            ->willReturn([$this->bccMock]);

        $this->bccMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($bcc[0]);

        $this->bccMock->expects(static::atLeastOnce())
            ->method('getEmail')
            ->willReturn($bcc[1]);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('addBcc')
            ->with($bcc[1], $bcc[0]);

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getCcList')
            ->willReturn([$this->ccMock]);

        $this->ccMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($cc[0]);

        $this->ccMock->expects(static::atLeastOnce())
            ->method('getEmail')
            ->willReturn($cc[1]);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('addCc')
            ->with($cc[1], $cc[0]);

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getSubject')
            ->willReturn($subject);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('setSubject')
            ->with($subject)
            ->willReturn($this->swiftMessageMock);

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getBody')
            ->willReturn($this->bodyMock);

        $this->bodyMock->expects(static::atLeastOnce())
            ->method('getPlainText')
            ->willReturn($body[0]);

        $this->bodyMock->expects(static::atLeastOnce())
            ->method('getHtml')
            ->willReturn($body[1]);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('setBody')
            ->with($body[1], 'text/html')
            ->willReturn($this->swiftMessageMock);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('addPart')
            ->with($body[0], 'text/plain')
            ->willReturn($this->swiftMessageMock);

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getAttachments')
            ->willReturn([$this->attachmentMock]);

        $this->swiftAttachmentFactoryMock->expects(static::atLeastOnce())
            ->method('create')
            ->with($this->attachmentMock)
            ->willReturn($this->swiftAttachmentMock);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('attach')
            ->with($this->swiftAttachmentMock)
            ->willReturn($this->swiftMessageMock);

        static::assertEquals(
            $this->mailHandler,
            $this->mailHandler->send($this->mailMock)
        );
    }

    /**
     * @return void
     */
    public function testSendWithMinimalData(): void
    {
        $from = ['John Doe', 'john.doe@example.com'];
        $to = ['Max Doe', 'max.doe@example.com'];
        $subject = 'Subject';
        $body = ['Body', '<p>Body</p>'];

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getFrom')
            ->willReturn($this->fromMock);

        $this->fromMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($from[0]);

        $this->fromMock->expects(static::atLeastOnce())
            ->method('getEmail')
            ->willReturn($from[1]);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('setFrom')
            ->with($from[1], $from[0])
            ->willReturn($this->swiftMessageMock);

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getToList')
            ->willReturn([$this->toMock]);

        $this->toMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn($to[0]);

        $this->toMock->expects(static::atLeastOnce())
            ->method('getEmail')
            ->willReturn($to[1]);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('addTo')
            ->with($to[1], $to[0])
            ->willReturn($this->swiftMessageMock);

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getBccList')
            ->willReturn(null);

        $this->swiftMessageMock->expects(static::never())
            ->method('addBcc');

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getCcList')
            ->willReturn(null);

        $this->swiftMessageMock->expects(static::never())
            ->method('addCc');

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getSubject')
            ->willReturn($subject);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('setSubject')
            ->with($subject)
            ->willReturn($this->swiftMessageMock);

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getBody')
            ->willReturn($this->bodyMock);

        $this->bodyMock->expects(static::atLeastOnce())
            ->method('getPlainText')
            ->willReturn($body[0]);

        $this->bodyMock->expects(static::atLeastOnce())
            ->method('getHtml')
            ->willReturn($body[1]);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('setBody')
            ->with($body[1], 'text/html')
            ->willReturn($this->swiftMessageMock);

        $this->swiftMessageMock->expects(static::atLeastOnce())
            ->method('addPart')
            ->with($body[0], 'text/plain')
            ->willReturn($this->swiftMessageMock);

        $this->mailMock->expects(static::atLeastOnce())
            ->method('getAttachments')
            ->willReturn(null);

        $this->swiftAttachmentFactoryMock->expects(static::never())
            ->method('create');

        $this->swiftMessageMock->expects(static::never())
            ->method('attach');

        static::assertEquals(
            $this->mailHandler,
            $this->mailHandler->send($this->mailMock)
        );
    }
}
