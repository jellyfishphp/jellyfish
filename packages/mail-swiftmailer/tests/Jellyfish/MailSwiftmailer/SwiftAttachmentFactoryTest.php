<?php

namespace Jellyfish\MailSwiftmailer;

use Codeception\Test\Unit;
use Generated\Transfer\Mail\Attachment;

class SwiftAttachmentFactoryTest extends Unit
{
    /**
     * @var \Generated\Transfer\Mail\Attachment|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $attachmentMock;
    /**
     * @var \Jellyfish\MailSwiftmailer\SwiftAttachmentFactory
     */
    protected SwiftAttachmentFactory $swiftAttachmentFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->attachmentMock = $this->getMockBuilder(Attachment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->swiftAttachmentFactory = new SwiftAttachmentFactory();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $content = 'Foo Bar';
        $fileName = 'foobar.txt';
        $mimeType = 'text/plain';

        $this->attachmentMock->expects(static::atLeastOnce())
            ->method('getContent')
            ->willReturn($content);

        $this->attachmentMock->expects(static::atLeastOnce())
            ->method('getFileName')
            ->willReturn($fileName);

        $this->attachmentMock->expects(static::atLeastOnce())
            ->method('getMimeType')
            ->willReturn($mimeType);

        $swiftAttachment = $this->swiftAttachmentFactory->create($this->attachmentMock);

        static::assertEquals(
            $fileName,
            $swiftAttachment->getFilename()
        );

        static::assertEquals(
            $content,
            $swiftAttachment->getBody()
        );

        static::assertEquals(
            $mimeType,
            $swiftAttachment->getContentType()
        );
    }
}
