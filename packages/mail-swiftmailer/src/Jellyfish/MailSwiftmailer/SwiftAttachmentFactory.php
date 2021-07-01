<?php

namespace Jellyfish\MailSwiftmailer;

use Generated\Transfer\Mail\Attachment;
use Swift_Attachment as SwiftAttachment;

class SwiftAttachmentFactory implements SwiftAttachmentFactoryInterface
{
    /**
     * @param \Generated\Transfer\Mail\Attachment $attachment
     *
     * @return \Swift_Attachment
     */
    public function create(Attachment $attachment): SwiftAttachment
    {
        return new SwiftAttachment(
            $attachment->getContent(),
            $attachment->getFileName(),
            $attachment->getMimeType()
        );
    }
}
