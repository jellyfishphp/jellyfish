<?php

namespace Jellyfish\Event\Command;

use Codeception\Test\Unit;
use Jellyfish\Event\EventFacadeInterface;
use Jellyfish\Event\EventListenerInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;
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
     * @var \Jellyfish\Event\EventFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventFacadeMock;

    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serializerFacadeMock;

    /**
     * @var \Jellyfish\Event\EventListenerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventListenerMock;

    /**
     * @var \Jellyfish\Event\Command\EventListenerGetCommand
     */
    protected EventListenerGetCommand $eventListenerGetCommand;

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

        $this->eventFacadeMock = $this->getMockBuilder(EventFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializerFacadeMock = $this->getMockBuilder(SerializerFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventListenerMock = $this->getMockBuilder(EventListenerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventListenerGetCommand = new EventListenerGetCommand(
            $this->eventFacadeMock,
            $this->serializerFacadeMock
        );
    }

    /**
     * @return void
     */
    public function testGetName(): void
    {
        static::assertEquals(EventListenerGetCommand::NAME, $this->eventListenerGetCommand->getName());
    }

    /**
     * @return void
     */
    public function testGetDescription(): void
    {
        static::assertEquals(EventListenerGetCommand::DESCRIPTION, $this->eventListenerGetCommand->getDescription());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testRun(): void
    {
        $type = 'async';
        $asyncEventListeners = ['eventA' => [$this->eventListenerMock]];
        $json = '{"eventA":[{...}]}';

        $this->inputMock->expects(static::atLeastOnce())
            ->method('getArgument')
            ->with('type')
            ->willReturn($type);

        $this->eventFacadeMock->expects(static::atLeastOnce())
            ->method('getEventListenersByType')
            ->with($type)
            ->willReturn($asyncEventListeners);

        $this->serializerFacadeMock->expects(static::atLeastOnce())
            ->method('serialize')
            ->with((object) $asyncEventListeners)
            ->willReturn($json);

        $this->outputMock->expects(static::atLeastOnce())
            ->method('write')
            ->willReturn($json);

        $exitCode = $this->eventListenerGetCommand->run($this->inputMock, $this->outputMock);

        static::assertEquals(0, $exitCode);
    }
}
