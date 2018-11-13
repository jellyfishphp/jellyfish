<?php

namespace Jellyfish\QueueSQS;

use Aws\Sqs\SqsClient;
use Jellyfish\Queue\ClientInterface;
use Jellyfish\QueueSQS\Exception\CreateQueueException;

class Client implements ClientInterface
{
    /**
     * @var \Aws\Sqs\SqsClient
     */
    protected $sqsClient;

    /**
     * @param \Aws\Sqs\SqsClient $sqsClient
     */
    public function __construct(
        SqsClient $sqsClient
    ) {
        $this->sqsClient = $sqsClient;
    }


    /**
     * @param string $queueName
     *
     * @return string
     *
     * @throws \Jellyfish\QueueSQS\Exception\CreateQueueException
     */
    protected function declareQueue(string $queueName): string
    {
        $queueUrl = $this->getQueueUrl($queueName);

        if ($queueUrl === null) {
            $queueUrl = $this->createQueue($queueName);
        }

        return $queueUrl;
    }

    /**
     * @param string $queueName
     *
     * @return string
     */
    protected function getQueueUrl(string $queueName): ?string
    {
        $args = [
            'QueueName' => $queueName
        ];

        $result = $this->sqsClient->getQueueUrl($args);

        if (!$result->hasKey('QueueUrl')) {
            return null;
        }

        return $result->get('QueueUrl');
    }

    /**
     * @param string $queueName
     *
     * @return string
     *
     * @throws \Jellyfish\QueueSQS\Exception\CreateQueueException
     */
    protected function createQueue(string $queueName): string
    {
        $args = [
            'QueueName' => $queueName
        ];

        $result = $this->sqsClient->createQueue($args);

        if (!$result->hasKey('QueueUrl')) {
            throw new CreateQueueException(sprintf('Could not create queue "%s".', $queueName));
        }

        return $result->get('QueueUrl');
    }

    /**
     * @param string $queueName
     *
     * @return string|null
     *
     * @throws \Jellyfish\QueueSQS\Exception\CreateQueueException
     */
    public function receiveMessage(string $queueName): ?string
    {
        $queueUrl = $this->declareQueue($queueName);

        $args = [
            'QueueUrl' => $queueUrl,
            'MaxNumberOfMessages' => 1
        ];

        $result = $this->sqsClient->receiveMessage($args);

        return $result->search('Messages[0].Body');
    }

    /**
     * @param string $queueName
     * @param string $message
     *
     * @return \Jellyfish\Queue\ClientInterface
     *
     * @throws \Jellyfish\QueueSQS\Exception\CreateQueueException
     */
    public function sendMessage(string $queueName, string $message): ClientInterface
    {
        $queueUrl = $this->declareQueue($queueName);

        $args = [
            'QueueUrl' => $queueUrl,
            'MessageBody' => $message
        ];

        $this->sqsClient->sendMessage($args);

        return $this;
    }
}
