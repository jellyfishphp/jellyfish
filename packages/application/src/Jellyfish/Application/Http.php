<?php

declare(strict_types=1);

namespace Jellyfish\Application;

use Jellyfish\Http\HttpConstants;
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
        /** @var \Jellyfish\Http\HttpFacadeInterface $httpFacade */
        $httpFacade = $this->kernel->getContainer()->offsetGet(HttpConstants::FACADE);

        $response = $httpFacade->dispatch($httpFacade->getCurrentRequest());

        $httpFacade->emit($response);
    }
}
