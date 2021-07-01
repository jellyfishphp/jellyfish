<?php

namespace Jellyfish\MailSwiftmailer;

use Generated\Transfer\Mail;

interface MailHandlerInterface
{
    /**
     * @param \Generated\Transfer\Mail $mail
     *
     * @return \Jellyfish\MailSwiftmailer\MailHandlerInterface
     */
    public function send(Mail $mail): MailHandlerInterface;
}
