<?php

declare(strict_types=1);

namespace Jellyfish\Event\Command;

use Jellyfish\Event\EventListenerProviderInterface;
use Jellyfish\Serializer\SerializerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventListenerGetCommand extends Command
{
    public const NAME = 'event:listener:get';
    public const DESCRIPTION = 'Retrieve registered event listeners by type (JSON)';
    /**
     * @var \Jellyfish\Event\EventListenerProviderInterface
     */
    protected $eventListenerProvider;

    /**
     * @var \Jellyfish\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @param \Jellyfish\Event\EventListenerProviderInterface $eventListenerProvider
     * @param \Jellyfish\Serializer\SerializerInterface $serializer
     */
    public function __construct(
        EventListenerProviderInterface $eventListenerProvider,
        SerializerInterface $serializer
    ) {
        parent::__construct();

        $this->eventListenerProvider = $eventListenerProvider;
        $this->serializer = $serializer;
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

        $json = $this->serializer->serialize((object) $this->eventListenerProvider->getListenersByType($type), 'json');

        $output->write($json);

        return 0;
    }
}
