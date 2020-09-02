<?php

declare(strict_types=1);

namespace Jellyfish\QueueRabbitMq;

use PhpAmqpLib\Message\AMQPMessage;

class AmqpMessageFactory implements AmqpMessageFactoryInterface
{
    /**
     * @param string $body
     *
     * @return \PhpAmqpLib\Message\AMQPMessage
     */
    public function create(string $body): AMQPMessage
    {
        return new AMQPMessage($body, $this->getDefaultProperties());
    }

    /**
     * @return array
     */
    protected function getDefaultProperties(): array
    {
        return [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ];
    }
}
