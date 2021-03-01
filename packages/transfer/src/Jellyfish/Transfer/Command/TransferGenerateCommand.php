<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Command;

use Jellyfish\Log\LogFacadeInterface;
use Jellyfish\Transfer\TransferFacadeInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TransferGenerateCommand extends Command
{
    public const NAME = 'transfer:generate';
    public const DESCRIPTION = 'Generate transfer classes and factories';

    /**
     * @var \Jellyfish\Transfer\TransferFacadeInterface
     */
    protected $transferFacade;

    /**
     * @var \Jellyfish\Log\LogFacadeInterface
     */
    protected $logFacade;

    /**
     * @param \Jellyfish\Transfer\TransferFacadeInterface $transferFacade
     * @param \Jellyfish\Log\LogFacadeInterface $logFacade
     */
    public function __construct(
        TransferFacadeInterface $transferFacade,
        LogFacadeInterface $logFacade
    ) {
        $this->transferFacade = $transferFacade;
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
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->transferFacade->clean();
        $this->transferFacade->generate();

        return 0;
    }
}
