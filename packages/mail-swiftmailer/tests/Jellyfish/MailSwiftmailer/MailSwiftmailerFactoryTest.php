<?php

namespace Jellyfish\MailSwiftmailer;

use Codeception\Test\Unit;
use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Mail\MailConstants;

class MailSwiftmailerFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configFacadeMock;
    /**
     * @var \Jellyfish\MailSwiftmailer\MailSwiftmailerFactory
     */
    protected MailSwiftmailerFactory $factory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->configFacadeMock = $this->getMockBuilder(ConfigFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new MailSwiftmailerFactory(
            $this->configFacadeMock
        );
    }

    /**
     * @return void
     */
    public function testCreateMailHandler(): void
    {
        $this->configFacadeMock->expects(static::atLeastOnce())
            ->method('get')
            ->withConsecutive(
                [MailConstants::SMTP_HOST, MailConstants::DEFAULT_SMTP_HOST],
                [MailConstants::SMTP_PORT, MailConstants::DEFAULT_SMTP_PORT],
                [MailConstants::SMTP_ENCRYPTION, MailConstants::DEFAULT_SMTP_ENCRYPTION],
                [MailConstants::SMTP_AUTH_MODE, MailConstants::DEFAULT_SMTP_AUTH_MODE],
                [MailConstants::SMTP_USERNAME, MailConstants::DEFAULT_SMTP_USERNAME],
                [MailConstants::SMTP_PASSWORD, MailConstants::DEFAULT_SMTP_PASSWORD],
            )->willReturnOnConsecutiveCalls(
                MailConstants::DEFAULT_SMTP_HOST,
                MailConstants::DEFAULT_SMTP_PORT,
                MailConstants::DEFAULT_SMTP_ENCRYPTION,
                'plain',
                'john',
                'doe'
            );

        static::assertInstanceOf(MailHandler::class, $this->factory->createMailHandler());
    }
}
