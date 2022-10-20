<?php

declare(strict_types=1);

namespace Jellyfish\Event\Command;

use Codeception\Test\Unit;
use Exception;
use InvalidArgumentException;
use Jellyfish\Event\EventBulkListenerInterface;
use Jellyfish\Event\EventInterface;
use Jellyfish\Event\EventListenerInterface;
use Jellyfish\Event\EventListenerProviderInterface;
use Jellyfish\Event\EventQueueConsumerInterface;
use Jellyfish\Lock\LockFactoryInterface;
use Jellyfish\Lock\LockInterface;
use Jellyfish\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventListenerGetCommandTest extends Unit
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $inputMock;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $outputMock;

    /**
     * @var \Jellyfish\Event\EventListenerProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventListenerProviderMock;


    /**
     * @var \Jellyfish\Event\EventListenerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventListenerMock;

    /**
     * @var \Jellyfish\Event\Command\EventListenerGetCommand
     */
    protected $eventListenerGetCommand;

    /**
     * @var \Jellyfish\Serializer\SerializerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->inputMock = $this->getMockBuilder(InputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->outputMock = $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventListenerProviderMock = $this->getMockBuilder(EventListenerProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializerMock = $this->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventListenerGetCommand = new EventListenerGetCommand(
            $this->eventListenerProviderMock,
            $this->serializerMock
        );
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        $this->assertEquals(EventListenerGetCommand::NAME, $this->eventListenerGetCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        $this->assertEquals(EventListenerGetCommand::DESCRIPTION, $this->eventListenerGetCommand->getDescription());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRunWithLockedStatus(): void
    {
        $type = 'async';
        $asyncEventListeners = [
            'eventA' => [
                $this->eventListenerMock
            ]
        ];
        $json = '{"eventA":[{...}]}';


        $this->eventListenerProviderMock->expects($this->atLeastOnce())
            ->method('getListenersByType')
            ->with($type)
            ->willReturn($asyncEventListeners);


        $this->serializerMock->expects($this->atLeastOnce())
            ->method('serialize')
            ->with((object) $asyncEventListeners)
            ->willReturn($json);

        $this->outputMock->expects($this->atLeastOnce())
            ->method('write')
            ->willReturn($json);

        $exitCode = $this->eventListenerGetCommand->run($this->inputMock, $this->outputMock);

        $this->assertEquals(0, $exitCode);
    }
}
