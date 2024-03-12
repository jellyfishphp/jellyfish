<?php

namespace Jellyfish\Application;

use Jellyfish\Kernel\KernelInterface;
use Nyholm\Psr7\Response;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class RoadRunner
{
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
        /** @var \League\Route\Router $router */
        $router = $this->kernel->getContainer()->offsetGet('router');

        /** @var \Jellyfish\RoadRunner\RoadRunnerWorkerFactoryInterface $roadRunnerWorkerFactory */
        $roadRunnerWorkerFactory = $this->kernel->getContainer()->offsetGet('road-runner-worker-factory');

        $roadRunnerWorker = $roadRunnerWorkerFactory->create();

        while ($serverRequest = $roadRunnerWorker->waitRequest()) {
            try {
                $response = $router->dispatch($serverRequest);

                $roadRunnerWorker->respond($response);
            } catch (Throwable $throwable) {
                $roadRunnerWorker->respond(new Response(500, [], 'Something Went Wrong!'));
                $roadRunnerWorker->getWorker()->error($throwable->getMessage());
            }
        }
    }
}
