<?php

namespace Jellyfish\QueueSQS;

use Aws\Result;
use Aws\Sqs\SqsClient;
use Codeception\Test\Unit;
use Jellyfish\QueueSQS\Exception\CreateQueueException;

class ClientTest extends Unit
{
    /**
     * @var \Jellyfish\Queue\ClientInterface
     */
    protected $client;

    /**
     * @var \Aws\Sqs\SqsClient|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $sqsClientMock;

    /**
     * @var \Aws\Result[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected $resultMocks;

    /**
     * @var string
     */
    protected $queueName;

    /**
     * @var string
     */
    protected $queueUrl;

    /**
     * @var string
     */
    protected $message;

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    protected function _before(): void
    {
        $this->sqsClientMock = $this->getMockBuilder(SqsClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['getQueueUrl', 'createQueue', 'receiveMessage', 'sendMessage'])
            ->getMock();

        $this->resultMocks = [];

        $this->resultMocks[] = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultMocks[] = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultMocks[] = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->queueName = 'foo';
        $this->queueUrl = 'fooUrl';
        $this->message = '{"foo": "bar"}';

        $this->client = new Client($this->sqsClientMock);
    }

    /**
     * @return void
     */
    public function testReceiveMessageFromExistingQueue(): void
    {
        $this->resultMocks[0]->expects($this->atLeastOnce())
            ->method('hasKey')
            ->with('QueueUrl')
            ->willReturn(true);

        $this->resultMocks[0]->expects($this->atLeastOnce())
            ->method('get')
            ->with('QueueUrl')
            ->willReturn($this->queueUrl);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('getQueueUrl')
            ->with(['QueueName' => $this->queueName])
            ->willReturn($this->resultMocks[0]);

        $this->resultMocks[1]->expects($this->never())
            ->method('hasKey')
            ->with('QueueUrl')
            ->willReturn(true);

        $this->resultMocks[1]->expects($this->never())
            ->method('get')
            ->with('QueueUrl')
            ->willReturn($this->queueUrl);

        $this->sqsClientMock->expects($this->never())
            ->method('createQueue')
            ->with(['QueueName' => $this->queueName])
            ->willReturn($this->resultMocks[1]);

        $this->resultMocks[2]->expects($this->atLeastOnce())
            ->method('search')
            ->with('Messages[0].Body')
            ->willReturn($this->message);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('receiveMessage')
            ->with(['QueueUrl' => $this->queueUrl, 'MaxNumberOfMessages' => 1])
            ->willReturn($this->resultMocks[2]);

        $this->assertEquals($this->message, $this->client->receiveMessage($this->queueName));
    }

    /**
     * @return void
     */
    public function testReceiveMessageFromNonExistingQueue(): void
    {
        $this->resultMocks[0]->expects($this->atLeastOnce())
            ->method('hasKey')
            ->with('QueueUrl')
            ->willReturn(false);

        $this->resultMocks[0]->expects($this->never())
            ->method('get')
            ->with('QueueUrl')
            ->willReturn($this->queueUrl);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('getQueueUrl')
            ->with(['QueueName' => $this->queueName])
            ->willReturn($this->resultMocks[0]);

        $this->resultMocks[1]->expects($this->atLeastOnce())
            ->method('hasKey')
            ->with('QueueUrl')
            ->willReturn(true);

        $this->resultMocks[1]->expects($this->atLeastOnce())
            ->method('get')
            ->with('QueueUrl')
            ->willReturn($this->queueUrl);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('createQueue')
            ->with(['QueueName' => $this->queueName])
            ->willReturn($this->resultMocks[1]);

        $this->resultMocks[2]->expects($this->atLeastOnce())
            ->method('search')
            ->with('Messages[0].Body')
            ->willReturn($this->message);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('receiveMessage')
            ->with(['QueueUrl' => $this->queueUrl, 'MaxNumberOfMessages' => 1])
            ->willReturn($this->resultMocks[2]);

        $this->assertEquals($this->message, $this->client->receiveMessage($this->queueName));
    }

    /**
     * @return void
     */
    public function testReceiveMessage(): void
    {
        $this->resultMocks[0]->expects($this->atLeastOnce())
            ->method('hasKey')
            ->with('QueueUrl')
            ->willReturn(false);

        $this->resultMocks[0]->expects($this->never())
            ->method('get')
            ->with('QueueUrl')
            ->willReturn($this->queueUrl);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('getQueueUrl')
            ->with(['QueueName' => $this->queueName])
            ->willReturn($this->resultMocks[0]);

        $this->resultMocks[1]->expects($this->atLeastOnce())
            ->method('hasKey')
            ->with('QueueUrl')
            ->willReturn(false);

        $this->resultMocks[1]->expects($this->never())
            ->method('get')
            ->with('QueueUrl')
            ->willReturn($this->queueUrl);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('createQueue')
            ->with(['QueueName' => $this->queueName])
            ->willReturn($this->resultMocks[1]);

        $this->resultMocks[2]->expects($this->never())
            ->method('search')
            ->with('Messages[0].Body')
            ->willReturn($this->message);

        $this->sqsClientMock->expects($this->never())
            ->method('receiveMessage')
            ->with(['QueueUrl' => $this->queueUrl, 'MaxNumberOfMessages' => 1])
            ->willReturn($this->resultMocks[2]);

        try {
            $this->client->receiveMessage($this->queueName);
            $this->fail();
        } catch (CreateQueueException $e) {
        }
    }

    /**
     * @return void
     */
    public function testSendMessageToExistingQueue(): void
    {
        $this->resultMocks[0]->expects($this->atLeastOnce())
            ->method('hasKey')
            ->with('QueueUrl')
            ->willReturn(true);

        $this->resultMocks[0]->expects($this->atLeastOnce())
            ->method('get')
            ->with('QueueUrl')
            ->willReturn($this->queueUrl);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('getQueueUrl')
            ->with(['QueueName' => $this->queueName])
            ->willReturn($this->resultMocks[0]);

        $this->resultMocks[1]->expects($this->never())
            ->method('hasKey')
            ->with('QueueUrl')
            ->willReturn(true);

        $this->resultMocks[1]->expects($this->never())
            ->method('get')
            ->with('QueueUrl')
            ->willReturn($this->queueUrl);

        $this->sqsClientMock->expects($this->never())
            ->method('createQueue')
            ->with(['QueueName' => $this->queueName])
            ->willReturn($this->resultMocks[1]);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('sendMessage')
            ->with([
                'QueueUrl' => $this->queueUrl,
                'MessageBody' => $this->message
            ]);

        $this->assertEquals($this->client, $this->client->sendMessage($this->queueName, $this->message));
    }

    /**
     * @return void
     */
    public function testSendMessageToNonExistingQueue(): void
    {
        $this->resultMocks[0]->expects($this->atLeastOnce())
            ->method('hasKey')
            ->with('QueueUrl')
            ->willReturn(false);

        $this->resultMocks[0]->expects($this->never())
            ->method('get')
            ->with('QueueUrl')
            ->willReturn($this->queueUrl);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('getQueueUrl')
            ->with(['QueueName' => $this->queueName])
            ->willReturn($this->resultMocks[0]);

        $this->resultMocks[1]->expects($this->atLeastOnce())
            ->method('hasKey')
            ->with('QueueUrl')
            ->willReturn(true);

        $this->resultMocks[1]->expects($this->atLeastOnce())
            ->method('get')
            ->with('QueueUrl')
            ->willReturn($this->queueUrl);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('createQueue')
            ->with(['QueueName' => $this->queueName])
            ->willReturn($this->resultMocks[1]);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('sendMessage')
            ->with([
                'QueueUrl' => $this->queueUrl,
                'MessageBody' => $this->message
            ]);

        $this->assertEquals($this->client, $this->client->sendMessage($this->queueName, $this->message));
    }

    /**
     * @return void
     */
    public function testSendMessage(): void
    {
        $this->resultMocks[0]->expects($this->atLeastOnce())
            ->method('hasKey')
            ->with('QueueUrl')
            ->willReturn(false);

        $this->resultMocks[0]->expects($this->never())
            ->method('get')
            ->with('QueueUrl')
            ->willReturn($this->queueUrl);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('getQueueUrl')
            ->with(['QueueName' => $this->queueName])
            ->willReturn($this->resultMocks[0]);

        $this->resultMocks[1]->expects($this->atLeastOnce())
            ->method('hasKey')
            ->with('QueueUrl')
            ->willReturn(false);

        $this->resultMocks[1]->expects($this->never())
            ->method('get')
            ->with('QueueUrl')
            ->willReturn($this->queueUrl);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('createQueue')
            ->with(['QueueName' => $this->queueName])
            ->willReturn($this->resultMocks[1]);

        $this->sqsClientMock->expects($this->never())
            ->method('sendMessage')
            ->with([
                'QueueUrl' => $this->queueUrl,
                'MessageBody' => $this->message
            ]);

        try {
            $this->client->receiveMessage($this->queueName);
            $this->fail();
        } catch (CreateQueueException $e) {
        }
    }
}
