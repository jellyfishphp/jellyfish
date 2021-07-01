<?php

namespace Jellyfish\MailSwiftmailer;

use Generated\Transfer\Mail;
use Jellyfish\Mail\MailFacadeInterface;

class MailSwiftmailerFacade implements MailFacadeInterface
{
    /**
     * @var \Jellyfish\MailSwiftmailer\MailSwiftmailerFactory
     */
    protected MailSwiftmailerFactory $factory;

    /**
     * @param \Jellyfish\MailSwiftmailer\MailSwiftmailerFactory $factory
     */
    public function __construct(MailSwiftmailerFactory $factory)
    {
        $this->factory = $factory;
    }


    /**
     * @param \Generated\Transfer\Mail $mail
     *
     * @return \Jellyfish\Mail\MailFacadeInterface
     */
    public function send(Mail $mail): MailFacadeInterface
    {
        $this->factory->createMailHandler()->send($mail);

        return $this;
    }
}
