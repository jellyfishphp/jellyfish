<?php

declare(strict_types=1);

namespace Jellyfish\Codeception\Module;

use Codeception\Module;
use Jellyfish\Codeception\Lib\TransferFacadeFactory;
use Jellyfish\Transfer\TransferFacadeInterface;

class Jellyfish extends Module
{
    /**
     * @var array
     */
    protected $config = [
        JellyfishConstants::CONFIG_GENERATE_TRANSFER_CLASSES => false
    ];

    /**
     * @var \Jellyfish\Transfer\TransferFacadeInterface|null
     */
    protected ?TransferFacadeInterface $transferFacade = null;

    /**
     * @return \Jellyfish\Transfer\TransferFacadeInterface
     */
    protected function getTransferFacade(): TransferFacadeInterface
    {
        if ($this->transferFacade === null) {
            // @codeCoverageIgnoreStart
            $this->transferFacade = (new TransferFacadeFactory())->create();
            // @codeCoverageIgnoreEnd
        }

        return $this->transferFacade;
    }

    /**
     * @return void
     *
     * @phpcs:disable
     */
    public function _initialize(): void
    {
        parent::_initialize();
        // @phpcs:enable

        if ((bool)$this->config[JellyfishConstants::CONFIG_GENERATE_TRANSFER_CLASSES]) {
            $this->generateTransferClasses();
        }
    }

    /**
     * @return void
     */
    protected function generateTransferClasses(): void
    {
        $this->debug('Deleting existing transfer classes...');
        $this->getTransferFacade()->clean();

        $this->debug('Creating transfer classes...');
        $this->getTransferFacade()->generate();
    }
}
