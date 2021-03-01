<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use Codeception\Test\Unit;
use PhpAmqpLib\Message\AMQPMessage;

class AmqpMessageFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\QueueRabbitMq\AmqpMessageFactory
     */
    protected $amqpMessageFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->amqpMessageFactory = new AmqpMessageFactory();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $body = '{"foo": "bar"}';

        $amqpMessage = $this->amqpMessageFactory->create($body);

        static::assertEquals($body, $amqpMessage->getBody());
        static::assertArrayHasKey('delivery_mode', $amqpMessage->get_properties());
        static::assertEquals(AMQPMessage::DELIVERY_MODE_PERSISTENT, $amqpMessage->get_properties()['delivery_mode']);
    }
}
