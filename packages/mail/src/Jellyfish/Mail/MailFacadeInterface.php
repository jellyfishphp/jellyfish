<?php

declare(strict_types=1);

namespace Jellyfish\Mail;

use Generated\Transfer\Mail;

interface MailFacadeInterface
{
    /**
     * @param \Generated\Transfer\Mail $mail
     *
     * @return \Jellyfish\Mail\MailFacadeInterface
     */
    public function send(Mail $mail): MailFacadeInterface;
}
