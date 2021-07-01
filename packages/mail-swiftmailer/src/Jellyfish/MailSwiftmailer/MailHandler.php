<?php

namespace Jellyfish\MailSwiftmailer;

use Generated\Transfer\Mail;
use Swift_Mailer as SwiftMailer;
use Swift_Message as SwiftMessage;

class MailHandler implements MailHandlerInterface
{
    protected const BODY_TYPE_HTML = 'text/html';
    protected const BODY_TYPE_PLAIN = 'text/plain';

    /**
     * @var \Swift_Mailer
     */
    protected SwiftMailer $swiftMailer;

    /**
     * @var \Swift_Message
     */
    protected SwiftMessage $swiftMessage;

    /**
     * @var \Jellyfish\MailSwiftmailer\SwiftAttachmentFactoryInterface
     */
    protected SwiftAttachmentFactoryInterface $swiftAttachmentFactory;

    /**
     * @param \Swift_Mailer $swiftMailer
     * @param \Swift_Message $swiftMessage
     * @param \Jellyfish\MailSwiftmailer\SwiftAttachmentFactoryInterface $swiftAttachmentFactory
     */
    public function __construct(
        SwiftMailer $swiftMailer,
        SwiftMessage $swiftMessage,
        SwiftAttachmentFactoryInterface $swiftAttachmentFactory
    ) {
        $this->swiftMailer = $swiftMailer;
        $this->swiftMessage = $swiftMessage;
        $this->swiftAttachmentFactory = $swiftAttachmentFactory;
    }

    /**
     * @param \Generated\Transfer\Mail $mail
     *
     * @return \Jellyfish\MailSwiftmailer\MailHandlerInterface
     */
    public function send(Mail $mail): MailHandlerInterface
    {
        $this->addFrom($mail)
            ->addTo($mail)
            ->addCc($mail)
            ->addBcc($mail)
            ->addSubject($mail)
            ->addBody($mail)
            ->addAttachments($mail);

        $this->swiftMailer->send($this->swiftMessage);

        return $this;
    }

    /**
     * @param \Generated\Transfer\Mail $mail
     *
     * @return \Jellyfish\MailSwiftmailer\MailHandler
     */
    protected function addFrom(Mail $mail): MailHandler
    {
        $from = $mail->getFrom();

        $this->swiftMessage->setFrom($from->getEmail(), $from->getName());

        return $this;
    }

    /**
     * @param \Generated\Transfer\Mail $mail
     *
     * @return \Jellyfish\MailSwiftmailer\MailHandler
     */
    protected function addTo(Mail $mail): MailHandler
    {
        $toList = $mail->getToList();

        foreach ($toList as $to) {
            $this->swiftMessage->addTo($to->getEmail(), $to->getName());
        }

        return $this;
    }

    /**
     * @param \Generated\Transfer\Mail $mail
     *
     * @return \Jellyfish\MailSwiftmailer\MailHandler
     */
    protected function addBcc(Mail $mail): MailHandler
    {
        $bccList = $mail->getBccList();

        if ($bccList === null) {
            return $this;
        }

        foreach ($bccList as $bcc) {
            $this->swiftMessage->addBcc($bcc->getEmail(), $bcc->getName());
        }

        return $this;
    }

    /**
     * @param \Generated\Transfer\Mail $mail
     *
     * @return \Jellyfish\MailSwiftmailer\MailHandler
     */
    protected function addCc(Mail $mail): MailHandler
    {
        $ccList = $mail->getCcList();

        if ($ccList === null) {
            return $this;
        }

        foreach ($ccList as $cc) {
            $this->swiftMessage->addCc($cc->getEmail(), $cc->getName());
        }

        return $this;
    }

    /**
     * @param \Generated\Transfer\Mail $mail
     *
     * @return \Jellyfish\MailSwiftmailer\MailHandler
     */
    protected function addSubject(Mail $mail): MailHandler
    {
        $this->swiftMessage->setSubject($mail->getSubject());

        return $this;
    }

    /**
     * @param \Generated\Transfer\Mail $mail
     *
     * @return \Jellyfish\MailSwiftmailer\MailHandler
     */
    protected function addBody(Mail $mail): MailHandler
    {
        $body = $mail->getBody();

        $this->swiftMessage->setBody($body->getHtml(), static::BODY_TYPE_HTML)
            ->addPart($body->getPlainText(), static::BODY_TYPE_PLAIN);

        return $this;
    }

    /**
     * @param \Generated\Transfer\Mail $mail
     *
     * @return \Jellyfish\MailSwiftmailer\MailHandler
     */
    protected function addAttachments(Mail $mail): MailHandler
    {
        $attachments = $mail->getAttachments();

        if ($attachments === null) {
            return $this;
        }

        foreach ($attachments as $attachment) {
            $this->swiftMessage->attach($this->swiftAttachmentFactory->create($attachment));
        }

        return $this;
    }
}
