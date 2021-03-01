<?php

declare(strict_types=1);

namespace Jellyfish\FinderSymfony;

use Codeception\Test\Unit;

class FinderSymfonyFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\FinderSymfony\FinderSymfonyFacade
     */
    protected $finderSymfonyFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->finderSymfonyFactory = new FinderSymfonyFactory();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        static::assertInstanceOf(Finder::class, $this->finderSymfonyFactory->createFinder());
    }
}
