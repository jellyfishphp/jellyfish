<?php

declare(strict_types=1);

namespace Jellyfish\Codeception\Lib;

use Codeception\Test\Unit;
use Jellyfish\Transfer\TransferFacade;

class TransferFacadeFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\Codeception\Lib\TransferFacadeFactory
     */
    protected TransferFacadeFactory $transferFacadeFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->transferFacadeFactory = new TransferFacadeFactory();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        static::assertInstanceOf(TransferFacade::class, $this->transferFacadeFactory->create());
    }
}
