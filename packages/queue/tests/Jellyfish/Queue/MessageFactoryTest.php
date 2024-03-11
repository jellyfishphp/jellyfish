<?php

declare(strict_types = 1);

namespace Jellyfish\Queue;

use Codeception\Test\Unit;

class MessageFactoryTest extends Unit
{
    protected MessageFactory $messageFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->messageFactory = new MessageFactory();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $message = $this->messageFactory->create();

        $this->assertInstanceOf(Message::class, $message);
    }
}
