<?php

namespace Jellyfish\RoadRunner;

use Nyholm\Psr7\Factory\Psr17Factory;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Http\PSR7WorkerInterface;
use Spiral\RoadRunner\Worker;

/**
 * @codeCoverageIgnore
 */
class RoadRunnerWorkerFactory implements RoadRunnerWorkerFactoryInterface
{
    protected Psr17Factory $psr17Factory;

    protected ?PSR7WorkerInterface $roadRunnerWorker = null;

    /**
     * @param \Nyholm\Psr7\Factory\Psr17Factory $psr17Factory
     */
    public function __construct(Psr17Factory $psr17Factory)
    {
        $this->psr17Factory = $psr17Factory;
    }

    /**
     * @return \Spiral\RoadRunner\Http\PSR7WorkerInterface
     */
    public function create(): PSR7WorkerInterface
    {
        if (!$this->roadRunnerWorker instanceof PSR7WorkerInterface) {
            $this->roadRunnerWorker = new PSR7Worker(
                Worker::create(),
                $this->psr17Factory,
                $this->psr17Factory,
                $this->psr17Factory
            );
        }

        return $this->roadRunnerWorker;
    }
}
