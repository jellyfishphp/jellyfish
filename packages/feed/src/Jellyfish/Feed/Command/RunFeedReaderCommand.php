<?php

declare(strict_types=1);

namespace Jellyfish\Feed\Command;

use InvalidArgumentException;
use Jellyfish\Feed\FeedReaderManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function is_string;

/**
 * @see \Jellyfish\Feed\Command\RunFeedReaderCommandTest
 */
class RunFeedReaderCommand extends Command
{
    public const NAME = 'feed:feed-reader:run';

    public const DESCRIPTION = 'Run feed reader.';

    protected FeedReaderManagerInterface $feedReaderManager;

    /**
     * @param \Jellyfish\Feed\FeedReaderManagerInterface $feedReaderManager
     */
    public function __construct(FeedReaderManagerInterface $feedReaderManager)
    {
        parent::__construct();

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
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('identifier');

        if (!is_string($identifier)) {
            throw new InvalidArgumentException('Unsupported type for given argument');
        }

        $this->feedReaderManager->readFromFeedReader($identifier);

        return 0;
    }
}
