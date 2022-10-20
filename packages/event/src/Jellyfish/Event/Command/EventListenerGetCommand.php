<?php

declare(strict_types=1);

namespace Jellyfish\Event\Command;

use Jellyfish\Event\EventFacadeInterface;
use Jellyfish\Serializer\SerializerFacadeInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventListenerGetCommand extends Command
{
    public const NAME = 'event:listener:get';
    public const DESCRIPTION = 'Retrieve registered event listeners by type (JSON)';

    /**
     * @var \Jellyfish\Event\EventFacadeInterface
     */
    protected EventFacadeInterface $eventFacade;
    /**
     * @var \Jellyfish\Serializer\SerializerFacadeInterface
     */
    protected SerializerFacadeInterface $serializerFacade;

    /**
     * @param \Jellyfish\Event\EventFacadeInterface $eventFacade
     * @param \Jellyfish\Serializer\SerializerFacadeInterface $serializerFacade
     */
    public function __construct(
        EventFacadeInterface $eventFacade,
        SerializerFacadeInterface $serializerFacade
    ) {
        parent::__construct();

        $this->eventFacade = $eventFacade;
        $this->serializerFacade = $serializerFacade;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::NAME);
        $this->setDescription(static::DESCRIPTION);

        $this->addArgument('type', InputArgument::REQUIRED, 'Type');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $input->getArgument('type');

        $json = $this->serializerFacade->serialize((object) $this->eventFacade->getEventListenersByType($type), 'json');

        $output->write($json);

        return 0;
    }
}
