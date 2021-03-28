<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

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

    /**
     * @return void
     */
    public function testGetHeaders(): void
    {
        static::assertCount(0, $this->message->getHeaders());
    }

    /**
     * @return void
     */
    public function testSetAndGetHeaders(): void
    {
        $headers = [
            'test' => 'test'
        ];

        $this->message->setHeaders($headers);
        $actualHeaders = $this->message->getHeaders();

        static::assertCount(1, $actualHeaders);
        static::assertEquals($headers, $actualHeaders);
    }

    /**
     * @return void
     */
    public function testSetAndGetBody(): void
    {
        $body = 'Test';
        $this->message->setBody($body);
        static::assertEquals($body, $this->message->getBody());
    }

    /**
     * @return void
     */
    public function testSetAndGetHeader(): void
    {
        $headerKey = 'test';
        $headerValue = 'test';

        $this->message->setHeader($headerKey, $headerValue);
        static::assertEquals($headerValue, $this->message->getHeader($headerKey));
    }

    /**
     * @return void
     */
    public function testGetNonExistingHeader(): void
    {
        $headerKey = 'test';

        static::assertNull($this->message->getHeader($headerKey));
    }
}
