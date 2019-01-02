<?php

namespace Jellyfish\Queue;

use Codeception\Test\Unit;

class MessageTest extends Unit
{
    /**
     * @var \Jellyfish\Queue\MessageInterface
     */
    protected $message;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->message = new Message();
    }

    public function testSetAndGetBody(): void
    {
        $body = 'Test';
        $this->message->setBody($body);
        $this->assertEquals($body, $this->message->getBody());
    }

    /**
     * @return void
     */
    public function testSetAndGetHeader(): void
    {
        $headerKey = 'test';
        $headerValue = 'test';

        $this->message->setHeader($headerKey, $headerValue);
        $this->assertEquals($headerValue, $this->message->getHeader($headerKey));
    }

    /**
     * @return void
     */
    public function testGetNonExistingHeader(): void
    {
        $headerKey = 'test';

        $this->assertNull($this->message->getHeader($headerKey));
    }
}
