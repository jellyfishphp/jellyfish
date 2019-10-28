<?php

declare(strict_types=1);

namespace Jellyfish\Application;

use Jellyfish\Kernel\KernelInterface;

class Http
{
    /**
     * @var \Jellyfish\Kernel\KernelInterface
     */
    protected $kernel;

    /**
     * @param \Jellyfish\Kernel\KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @return void
     */
    public function run(): void
    {
        /** @var \Psr\Http\Message\ServerRequestInterface $request */
        $request = $this->kernel->getContainer()->offsetGet('request');
        /** @var \League\Route\Router $router */
        $router = $this->kernel->getContainer()->offsetGet('router');
        /** @var \Zend\HttpHandlerRunner\Emitter\EmitterInterface $emitter */
        $emitter = $this->kernel->getContainer()->offsetGet('emitter');

        $response = $router->dispatch($request);

        $emitter->emit($response);
    }
}
