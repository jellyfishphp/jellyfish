<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

class TransferFacade implements TransferFacadeInterface
{
    /**
     * @var \Jellyfish\Transfer\TransferFactory
     */
    protected $factory;

    /**
     * @param \Jellyfish\Transfer\TransferFactory $factory
     */
    public function __construct(TransferFactory $factory)
    {
        $this->factory = $factory;
    }


    /**
     * @return \Jellyfish\Transfer\TransferFacadeInterface
     */
    public function generate(): TransferFacadeInterface
    {
        $this->factory->getTransferGenerator()->generate();

        return $this;
    }

    /**
     * @return \Jellyfish\Transfer\TransferFacadeInterface
     */
    public function clean(): TransferFacadeInterface
    {
        $this->factory->getTransferCleaner()->clean();

        return $this;
    }
}
