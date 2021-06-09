<?php

declare(strict_types=1);

namespace Jellyfish\RoadRunner;

use Nyholm\Psr7\Factory\Psr17Factory;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Http\PSR7WorkerInterface;
use Spiral\RoadRunner\WorkerInterface as RoadRunnerWorkerInterface;
use Spiral\RoadRunner\Worker as RoadRunnerWorker;

class RoadRunnerFactory
{
    /**
     * @var \Jellyfish\RoadRunner\WorkerInterface|null
     */
    protected ?WorkerInterface $worker = null;

    /**
     * @var \Spiral\RoadRunner\Http\PSR7WorkerInterface|null
     */
    protected ?PSR7WorkerInterface $psr7Worker = null;

    /**
     * @var \Spiral\RoadRunner\WorkerInterface|null
     */
    protected ?RoadRunnerWorkerInterface $roadRunnerWorker = null;

    /**
     * @var \Nyholm\Psr7\Factory\Psr17Factory|null
     */
    protected ?Psr17Factory $psr17Factory = null;

    /**
     * @return \Jellyfish\RoadRunner\WorkerInterface
     */
    public function getWorker(): WorkerInterface
    {
        if ($this->worker === null) {
            $this->worker = new Worker($this->getPsr7Worker());
        }

        return $this->worker;
    }

    /**
     * @return \Spiral\RoadRunner\Http\PSR7WorkerInterface
     */
    protected function getPsr7Worker(): PSR7WorkerInterface
    {
        if ($this->psr7Worker === null) {
            $psr17Factory = $this->getPsr17Factory();
            $roadRunnerWorker = $this->getRoadRunnerWorker();

            $this->psr7Worker = new PSR7Worker($roadRunnerWorker, $psr17Factory, $psr17Factory, $psr17Factory);
        }

        return $this->psr7Worker;
    }

    /**
     * @return \Spiral\RoadRunner\WorkerInterface
     */
    protected function getRoadRunnerWorker(): RoadRunnerWorkerInterface
    {
        if ($this->roadRunnerWorker === null) {
            $this->roadRunnerWorker = RoadRunnerWorker::create();
        }

        return $this->roadRunnerWorker;
    }

    /**
     * @return \Nyholm\Psr7\Factory\Psr17Factory
     */
    protected function getPsr17Factory(): Psr17Factory
    {
        if ($this->psr17Factory === null) {
            $this->psr17Factory = new Psr17Factory();
        }

        return $this->psr17Factory;
    }
}
