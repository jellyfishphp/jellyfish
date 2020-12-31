<?php

namespace Jellyfish\HttpLeague;

use Codeception\Test\Unit;
use League\Route\Router;
use Zend\Diactoros\ServerRequest;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

class HttpLeagueFactoryTest extends Unit
{
    /**
     * @var \Jellyfish\HttpLeague\HttpLeagueFactory
     */
    protected $httpLeagueFactory;

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
    public function testCreateRouter(): void
    {
        static::assertInstanceOf(
            Router::class,
            $this->httpLeagueFactory->createRouter()
        );
    }

    /**
     * @return void
     */
    public function testCreateRequest(): void
    {
        static::assertInstanceOf(
            ServerRequest::class,
            $this->httpLeagueFactory->createRequest()
        );
    }

    /**
     * @return void
     */
    public function testCreateEmitter(): void
    {
        static::assertInstanceOf(
            SapiStreamEmitter::class,
            $this->httpLeagueFactory->createEmitter()
        );
    }
}
