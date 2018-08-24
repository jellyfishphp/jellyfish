<?php

namespace Jellyfish\Application;

use Codeception\Test\Unit;
use Pimple\Container;

class ApplicationTest extends Unit
{
    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @var \Jellyfish\Application\Application
     */
    protected $application;

    /**
     * @return void
     *
     * @throws \Exception
     */
    protected function _before(): void
    {
        parent::_before();

        $this->container = new Container();
    }

    /**
     * @return void
     */
    public function testRun(): void
    {

    }
}
