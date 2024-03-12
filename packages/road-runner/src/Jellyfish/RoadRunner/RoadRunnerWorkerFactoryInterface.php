<?php

namespace Jellyfish\RoadRunner;

use Spiral\RoadRunner\Http\PSR7WorkerInterface;

interface RoadRunnerWorkerFactoryInterface
{
    /**
     * @return \Spiral\RoadRunner\Http\PSR7WorkerInterface
     */
    public function create(): PSR7WorkerInterface;
}
