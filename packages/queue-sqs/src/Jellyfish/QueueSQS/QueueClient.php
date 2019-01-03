<?php

namespace Jellyfish\QueueSQS;

use Aws\Sqs\SqsClient;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\MessageMapperInterface;
use Jellyfish\Queue\QueueClientInterface;
use Jellyfish\QueueSQS\Exception\CreateQueueException;

class QueueClient implements QueueClientInterface
{
    /**
     * @var \Aws\Sqs\SqsClient
     */
    protected $sqsClient;

    /**
     * @var \Jellyfish\Queue\MessageMapperInterface
     */
    protected $messageMapper;

    /**
     * @param \Aws\Sqs\SqsClient $sqsClient
     * @param MessageMapperInterface $messageMapper
     */
    public function __construct(
        SqsClient $sqsClient,
        MessageMapperInterface $messageMapper
    ) {
        $this->sqsClient = $sqsClient;
        $this->messageMapper = $messageMapper;
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
     * @return \Jellyfish\Queue\MessageInterface|null
     *
     * @throws \Jellyfish\QueueSQS\Exception\CreateQueueException
     */
    public function receiveMessage(string $queueName): ?MessageInterface
    {
        $queueUrl = $this->declareQueue($queueName);

        $args = [
            'QueueUrl' => $queueUrl,
            'MaxNumberOfMessages' => 1
        ];

        $result = $this->sqsClient->receiveMessage($args);
        $messageAsJson = $result->search('Messages[0].Body');

        if ($messageAsJson === null) {
            return null;
        }

        return $this->messageMapper->fromJson($messageAsJson);
    }

    /**
     * @param string $queueName
     * @param \Jellyfish\Queue\MessageInterface $message
     *
     * @return \Jellyfish\Queue\QueueClientInterface
     *
     * @throws \Jellyfish\QueueSQS\Exception\CreateQueueException
     */
    public function sendMessage(string $queueName, MessageInterface $message): QueueClientInterface
    {
        $queueUrl = $this->declareQueue($queueName);
        $json = $this->messageMapper->toJson($message);

        $args = [
            'QueueUrl' => $queueUrl,
            'MessageBody' => $json
        ];

        $this->sqsClient->sendMessage($args);

        return $this;
    }
}