<?php

namespace Jellyfish\QueueSQS;

use Aws\Sqs\SqsClient;
use Jellyfish\Config\ConfigInterface;
use Jellyfish\Queue\ClientInterface;
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
        $self = $this;

        $pimple->offsetSet('queue_client', function ($container) use ($self) {
            return $self->createClient($container['config']);
        });
    }

    /**
     * @param \Jellyfish\Config\ConfigInterface $config
     *
     * @return \Jellyfish\Queue\ClientInterface
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     */
    protected function createClient(ConfigInterface $config): ClientInterface
    {
        $sqsClient = $this->createSqsClient($config);

        return new Client($sqsClient);
    }

    /**
     * @param \Jellyfish\Config\ConfigInterface $config
     *
     * @return \Aws\Sqs\SqsClient
     *
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     */
    protected function createSqsClient(ConfigInterface $config): SqsClient
    {
        $sqsConfig = [
            'region' => $config->get(QueueSQSConstants::SQS_REGION, QueueSQSConstants::DEFAULT_SQS_REGION),
            'profile' => $config->get(QueueSQSConstants::SQS_PROFILE, QueueSQSConstants::DEFAULT_SQS_PROFILE),
            'version' => $config->get(QueueSQSConstants::SQS_VERSION, QueueSQSConstants::DEFAULT_SQS_VERSION),
        ];

        return new SqsClient($sqsConfig);
    }
}
