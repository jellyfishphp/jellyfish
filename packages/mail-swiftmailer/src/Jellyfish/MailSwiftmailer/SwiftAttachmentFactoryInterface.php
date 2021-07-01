<?php

namespace Jellyfish\MailSwiftmailer;

use Generated\Transfer\Mail\Attachment;
use Swift_Attachment as SwiftAttachment;

interface SwiftAttachmentFactoryInterface
{
    /**
     * @return \Swift_Attachment
     */
    public function create(Attachment $attachment): SwiftAttachment;
}
