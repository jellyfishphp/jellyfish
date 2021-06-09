<?php

declare(strict_types=1);

namespace Jellyfish\Application;

use Jellyfish\Http\HttpConstants;
use Jellyfish\Kernel\KernelInterface;
use Jellyfish\RoadRunner\RoadRunnerConstants;
use Throwable;

class RoadRunner
{
    /**
     * @var \Jellyfish\Kernel\KernelInterface
     */
    protected KernelInterface $kernel;

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

        /** @var \Jellyfish\RoadRunner\RoadRunnerFacadeInterface $roadRunnerFacade */
        $roadRunnerFacade = $this->kernel->getContainer()->offsetGet(RoadRunnerConstants::FACADE);

        while ($serverRequest = $roadRunnerFacade->waitRequest()) {
            try {
                $response = $httpFacade->dispatch($serverRequest);

                $roadRunnerFacade->respond($response);
            } catch (Throwable $exception) {
                $roadRunnerFacade->error($exception->getMessage());
            }
        }
    }
}
