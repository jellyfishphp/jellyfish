<?php

declare(strict_types=1);

namespace Jellyfish\Transfer\Command;

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
    protected TransferFacadeInterface $transferFacade;

    /**
     * @param \Jellyfish\Transfer\TransferFacadeInterface $transferFacade
     */
    public function __construct(
        TransferFacadeInterface $transferFacade
    ) {
        $this->transferFacade = $transferFacade;

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
