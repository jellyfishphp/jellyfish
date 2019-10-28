<?php

declare(strict_types=1);

namespace Jellyfish\QueueSQS;

use Aws\Sqs\SqsClient;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QueueSQSServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->registerQueueClient($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\QueueSQS\QueueSQSServiceProvider
     */
    protected function registerQueueClient(Container $container): QueueSQSServiceProvider
    {
        $self = $this;

        $container->offsetSet('queue_client', function (Container $container) use ($self) {
            return new QueueClient(
                $self->createSqsClient($container),
                $container->offsetGet('message_mapper')
            );
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Aws\Sqs\SqsClient
     */
    protected function createSqsClient(Container $container): SqsClient
    {
        $config = $container->offsetGet('config');

        $sqsConfig = [
            'region' => $config->get(QueueSQSConstants::SQS_REGION, QueueSQSConstants::DEFAULT_SQS_REGION),
            'profile' => $config->get(QueueSQSConstants::SQS_PROFILE, QueueSQSConstants::DEFAULT_SQS_PROFILE),
            'version' => $config->get(QueueSQSConstants::SQS_VERSION, QueueSQSConstants::DEFAULT_SQS_VERSION),
        ];

        return new SqsClient($sqsConfig);
    }
}
