<?php

namespace Jellyfish\FinderSymfony;

use Codeception\Test\Unit;

class FinderFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\FinderSymfony\FinderFactory
     */
    protected $finderFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->finderFactory = new FinderFactory();
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $this->assertInstanceOf(Finder::class, $this->finderFactory->create());
    }
}
