<?php

declare(strict_types=1);

namespace Jellyfish\RoadRunner;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\RoadRunner\Http\PSR7WorkerInterface;

class Worker implements WorkerInterface
{
    /**
     * @var \Spiral\RoadRunner\Http\PSR7WorkerInterface
     */
    protected $psr7Worker;

    /**
     * @param \Spiral\RoadRunner\Http\PSR7WorkerInterface $psr7Worker
     */
    public function __construct(PSR7WorkerInterface $psr7Worker)
    {
        $this->psr7Worker = $psr7Worker;
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface|null
     */
    public function waitRequest(): ?ServerRequestInterface
    {
        return $this->psr7Worker->waitRequest();
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Jellyfish\RoadRunner\WorkerInterface
     */
    public function respond(ResponseInterface $response): WorkerInterface
    {
        $this->psr7Worker->respond($response);

        return $this;
    }

    /**
     * @param string $error
     *
     * @return \Jellyfish\RoadRunner\WorkerInterface
     */
    public function error(string $error): WorkerInterface
    {
        $this->psr7Worker->getWorker()->error($error);

        return $this;
    }
}
