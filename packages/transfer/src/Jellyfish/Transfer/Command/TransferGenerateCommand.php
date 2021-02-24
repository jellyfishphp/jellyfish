<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Command;

use Jellyfish\Log\LogFacadeInterface;
use Jellyfish\Transfer\TransferCleanerInterface;
use Jellyfish\Transfer\TransferGeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TransferGenerateCommand extends Command
{
    public const NAME = 'transfer:generate';
    public const DESCRIPTION = 'Generate transfer classes and factories';

    /**
     * @var \Jellyfish\Log\LogFacadeInterface
     */
    protected $logFacade;

    /**
     * @var \Jellyfish\Transfer\TransferGeneratorInterface
     */
    protected $transferGenerator;

    /**
     * @var \Jellyfish\Transfer\TransferCleanerInterface
     */
    protected $transferCleaner;

    /**
     * @param \Jellyfish\Transfer\TransferGeneratorInterface $transferGenerator
     * @param \Jellyfish\Transfer\TransferCleanerInterface $transferCleaner
     * @param \Jellyfish\Log\LogFacadeInterface $logFacade
     */
    public function __construct(
        TransferGeneratorInterface $transferGenerator,
        TransferCleanerInterface $transferCleaner,
        LogFacadeInterface $logFacade
    ) {
        $this->transferGenerator = $transferGenerator;
        $this->transferCleaner = $transferCleaner;
        $this->logFacade = $logFacade;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::NAME);
        $this->setDescription(static::DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->transferCleaner->clean();
        $this->transferGenerator->generate();

        return null;
    }
}
