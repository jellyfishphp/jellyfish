<?php

namespace Jellyfish\MailSwiftmailer;

use Jellyfish\Config\ConfigFacadeInterface;
use Jellyfish\Mail\MailConstants;
use Swift_Mailer as SwiftMailer;
use Swift_Message as SwiftMessage;
use Swift_SmtpTransport as SwiftSmtpTransport;
use Swift_Transport as SwiftTransport;

class MailSwiftmailerFactory
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface
     */
    protected ConfigFacadeInterface $configFacade;

    /**
     * @var \Swift_Mailer|null
     */
    protected ?SwiftMailer $swiftMailer = null;

    /**
     * @var \Swift_Transport|null
     */
    protected ?SwiftTransport $swiftTransport = null;

    /**
     * @var \Jellyfish\MailSwiftmailer\SwiftAttachmentFactory|null
     */
    protected ?SwiftAttachmentFactory $swiftAttachmentFactory = null;

    /**
     * @param \Jellyfish\Config\ConfigFacadeInterface $configFacade
     */
    public function __construct(ConfigFacadeInterface $configFacade)
    {
        $this->configFacade = $configFacade;
    }

    /**
     * @return \Jellyfish\MailSwiftmailer\MailHandlerInterface
     */
    public function createMailHandler(): MailHandlerInterface
    {
        return new MailHandler(
            $this->getSwiftMailer(),
            $this->createSwiftMessage(),
            $this->getSwiftAttachmentFactory()
        );
    }

    /**
     * @return \Swift_Mailer
     */
    protected function getSwiftMailer(): SwiftMailer
    {
        if ($this->swiftMailer === null) {
            $this->swiftMailer = new SwiftMailer($this->getSwiftTransport());
        }

        return $this->swiftMailer;
    }

    /**
     * @return \Swift_Transport
     */
    protected function getSwiftTransport(): SwiftTransport
    {
        if ($this->swiftTransport === null) {
            $this->swiftTransport = new SwiftSmtpTransport(
                $this->configFacade->get(MailConstants::SMTP_HOST, MailConstants::DEFAULT_SMTP_HOST),
                (int)$this->configFacade->get(MailConstants::SMTP_PORT, MailConstants::DEFAULT_SMTP_PORT),
                $this->configFacade->get(MailConstants::SMTP_ENCRYPTION, MailConstants::DEFAULT_SMTP_ENCRYPTION)
            );

            $authMode = $this->configFacade->get(MailConstants::SMTP_AUTH_MODE, MailConstants::DEFAULT_SMTP_AUTH_MODE);

            if ($authMode !== '') {
                $this->swiftTransport->setAuthMode($authMode)
                    ->setUsername($this->configFacade->get(MailConstants::SMTP_USERNAME, MailConstants::DEFAULT_SMTP_USERNAME))
                    ->setPassword($this->configFacade->get(MailConstants::SMTP_PASSWORD, MailConstants::DEFAULT_SMTP_PASSWORD));
            }
        }

        return $this->swiftTransport;
    }

    /**
     * @return \Swift_Message
     */
    protected function createSwiftMessage(): SwiftMessage
    {
        return new SwiftMessage();
    }

    /**
     * @return \Jellyfish\MailSwiftmailer\SwiftAttachmentFactory
     */
    protected function getSwiftAttachmentFactory(): SwiftAttachmentFactory
    {
        if ($this->swiftAttachmentFactory === null) {
            $this->swiftAttachmentFactory = new SwiftAttachmentFactory();
        }

        return $this->swiftAttachmentFactory;
    }
}
