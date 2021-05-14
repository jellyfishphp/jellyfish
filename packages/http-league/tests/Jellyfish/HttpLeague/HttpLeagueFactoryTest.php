<?php

declare(strict_types=1);

namespace Jellyfish\HttpLeague;

use Codeception\Test\Unit;
use Laminas\Diactoros\ServerRequest;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use League\Route\Router;

class HttpLeagueFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\HttpLeague\HttpLeagueFactory
     */
    protected HttpLeagueFactory $httpLeagueFactory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->httpLeagueFactory = new HttpLeagueFactory();
    }

    /**
     * @return void
     */
    public function testGetRouter(): void
    {
        static::assertInstanceOf(
            Router::class,
            $this->httpLeagueFactory->getRouter()
        );
    }

    /**
     * @return void
     */
    public function testGetRequest(): void
    {
        static::assertInstanceOf(
            ServerRequest::class,
            $this->httpLeagueFactory->getRequest()
        );
    }

    /**
     * @return void
     */
    public function testGetEmitter(): void
    {
        static::assertInstanceOf(
            SapiStreamEmitter::class,
            $this->httpLeagueFactory->getEmitter()
        );
    }
}
