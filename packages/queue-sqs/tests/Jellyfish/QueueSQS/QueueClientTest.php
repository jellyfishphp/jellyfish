<?php

declare(strict_types=1);

namespace Jellyfish\QueueSQS;

use Aws\Result;
use Aws\Sqs\SqsClient;
use Codeception\Test\Unit;
use Jellyfish\Queue\MessageInterface;
use Jellyfish\Queue\MessageMapperInterface;
use Jellyfish\QueueSQS\Exception\CreateQueueException;

class QueueClientTest extends Unit
{
    /**
     * @var \Jellyfish\Queue\QueueClientInterface
     */
    protected $queueClient;

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
    protected $messageAsJson;

    /**
     * @var \Jellyfish\Queue\MessageMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageMapperMock;

    /**
     * @var \Jellyfish\Queue\MessageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $messageMock;


    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    protected function _before(): void
    {
        $this->messageMapperMock = $this->getMockBuilder(MessageMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sqsClientMock = $this->getMockBuilder(SqsClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['getQueueUrl', 'createQueue', 'receiveMessage', 'receiveMessages', 'sendMessage'])
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
        $this->messageAsJson = '{"foo": "bar"}';

        $this->queueClient = new QueueClient($this->sqsClientMock, $this->messageMapperMock);
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
            ->willReturn($this->messageAsJson);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('receiveMessage')
            ->with(['QueueUrl' => $this->queueUrl, 'MaxNumberOfMessages' => 1])
            ->willReturn($this->resultMocks[2]);

        $this->messageMapperMock->expects($this->atLeastOnce())
            ->method('fromJson')
            ->with($this->messageAsJson)
            ->willReturn($this->messageMock);

        $this->assertEquals($this->messageMock, $this->queueClient->receiveMessage($this->queueName));
    }

    /**
     * @return void
     */
    public function testReceiveMessageFromEmptyQueue(): void
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
            ->willReturn(null);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('receiveMessage')
            ->with(['QueueUrl' => $this->queueUrl, 'MaxNumberOfMessages' => 1])
            ->willReturn($this->resultMocks[2]);

        $this->messageMapperMock->expects($this->never())
            ->method('fromJson')
            ->with($this->messageAsJson)
            ->willReturn($this->messageMock);

        $this->assertNull($this->queueClient->receiveMessage($this->queueName));
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
            ->willReturn($this->messageAsJson);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('receiveMessage')
            ->with(['QueueUrl' => $this->queueUrl, 'MaxNumberOfMessages' => 1])
            ->willReturn($this->resultMocks[2]);

        $this->messageMapperMock->expects($this->atLeastOnce())
            ->method('fromJson')
            ->with($this->messageAsJson)
            ->willReturn($this->messageMock);

        $this->assertEquals($this->messageMock, $this->queueClient->receiveMessage($this->queueName));
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
            ->willReturn($this->messageAsJson);

        $this->sqsClientMock->expects($this->never())
            ->method('receiveMessage')
            ->with(['QueueUrl' => $this->queueUrl, 'MaxNumberOfMessages' => 1])
            ->willReturn($this->resultMocks[2]);

        try {
            $this->queueClient->receiveMessage($this->queueName);
            $this->fail();
        } catch (CreateQueueException $e) {
        }
    }

    /**
     * @return void
     */
    public function testReceiveMessages(): void
    {
        $messageBody = '{"xxx": "yyy"}';
        $messages = [];

        for ($i = 0; $i < 10; $i++) {
            $messages[] = ['Body' => $messageBody];
        }

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
            ->with('QueueUrl');

        $this->resultMocks[1]->expects($this->never())
            ->method('get')
            ->with('QueueUrl');

        $this->sqsClientMock->expects($this->never())
            ->method('createQueue')
            ->with(['QueueName' => $this->queueName]);

        $this->resultMocks[2]->expects($this->atLeastOnce())
            ->method('get')
            ->with('Messages')
            ->willReturn($messages);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('receiveMessage')
            ->with(['QueueUrl' => $this->queueUrl, 'MaxNumberOfMessages' => 10])
            ->willReturn($this->resultMocks[2]);

        $this->messageMapperMock->expects($this->atLeastOnce())
            ->method('fromJson')
            ->with($messageBody)
            ->willReturn($this->messageMock);

        $receivedMessages = $this->queueClient->receiveMessages($this->queueName, 10);

        $this->assertCount(10, $receivedMessages);
        $this->assertEquals($this->messageMock, $receivedMessages[0]);
    }

    /**
     * @return void
     */
    public function testReceiveMessagesBelowLimit(): void
    {
        $messageBody = '{"xxx": "yyy"}';
        $messages = [];

        for ($i = 0; $i < 8; $i++) {
            $messages[] = ['Body' => $messageBody];
        }

        $messages[] = [];

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
            ->with('QueueUrl');

        $this->resultMocks[1]->expects($this->never())
            ->method('get')
            ->with('QueueUrl');

        $this->sqsClientMock->expects($this->never())
            ->method('createQueue')
            ->with(['QueueName' => $this->queueName]);

        $this->resultMocks[2]->expects($this->atLeastOnce())
            ->method('get')
            ->with('Messages')
            ->willReturn($messages);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('receiveMessage')
            ->with(['QueueUrl' => $this->queueUrl, 'MaxNumberOfMessages' => 10])
            ->willReturn($this->resultMocks[2]);

        $this->messageMapperMock->expects($this->atLeastOnce())
            ->method('fromJson')
            ->with($messageBody)
            ->willReturn($this->messageMock);

        $receivedMessages = $this->queueClient->receiveMessages($this->queueName, 11);

        $this->assertCount(8, $receivedMessages);
        $this->assertEquals($this->messageMock, $receivedMessages[0]);
    }

    /**
     * @return void
     */
    public function testReceiveMessagesWithInvalidResult(): void
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
            ->with('QueueUrl');

        $this->resultMocks[1]->expects($this->never())
            ->method('get')
            ->with('QueueUrl');

        $this->sqsClientMock->expects($this->never())
            ->method('createQueue')
            ->with(['QueueName' => $this->queueName]);

        $this->resultMocks[2]->expects($this->atLeastOnce())
            ->method('get')
            ->with('Messages')
            ->willReturn(null);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('receiveMessage')
            ->with(['QueueUrl' => $this->queueUrl, 'MaxNumberOfMessages' => 10])
            ->willReturn($this->resultMocks[2]);

        $this->messageMapperMock->expects($this->never())
            ->method('fromJson');

        $receivedMessages = $this->queueClient->receiveMessages($this->queueName, 11);

        $this->assertCount(0, $receivedMessages);
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

        $this->messageMapperMock->expects($this->atLeastOnce())
            ->method('toJson')
            ->with($this->messageMock)
            ->willReturn($this->messageAsJson);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('sendMessage')
            ->with([
                'QueueUrl' => $this->queueUrl,
                'MessageBody' => $this->messageAsJson
            ]);

        $this->assertEquals($this->queueClient, $this->queueClient->sendMessage($this->queueName, $this->messageMock));
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

        $this->messageMapperMock->expects($this->atLeastOnce())
            ->method('toJson')
            ->with($this->messageMock)
            ->willReturn($this->messageAsJson);

        $this->sqsClientMock->expects($this->atLeastOnce())
            ->method('sendMessage')
            ->with([
                'QueueUrl' => $this->queueUrl,
                'MessageBody' => $this->messageAsJson
            ]);

        $this->assertEquals($this->queueClient, $this->queueClient->sendMessage($this->queueName, $this->messageMock));
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

        $this->messageMapperMock->expects($this->never())
            ->method('toJson')
            ->with($this->messageMock)
            ->willReturn($this->messageAsJson);


        $this->sqsClientMock->expects($this->never())
            ->method('sendMessage')
            ->with([
                'QueueUrl' => $this->queueUrl,
                'MessageBody' => $this->messageAsJson
            ]);

        try {
            $this->queueClient->sendMessage($this->queueName, $this->messageMock);
            $this->fail();
        } catch (CreateQueueException $e) {
        }
    }
}
