<?php

namespace Jellyfish\Feed\Command;

use Jellyfish\Feed\FeedReaderManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunFeedReaderCommand extends Command
{
    public const NAME = 'feed:feed-reader:run';
    public const DESCRIPTION = 'Run feed reader.';

    /**
     * @var \Jellyfish\Feed\FeedReaderManagerInterface
     */
    protected $feedReaderManager;

    /**
     * @param \Jellyfish\Feed\FeedReaderManagerInterface $feedReaderManager
     */
    public function __construct(FeedReaderManagerInterface $feedReaderManager)
    {
        parent::__construct(null);

        $this->feedReaderManager = $feedReaderManager;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::NAME);
        $this->setDescription(static::DESCRIPTION);
        $this->addArgument('identifier', InputArgument::REQUIRED, 'Feed reader identifier.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $identifier = (string) $input->getArgument('identifier');

        $this->feedReaderManager->readFromFeedReader($identifier);

        return null;
    }
}
