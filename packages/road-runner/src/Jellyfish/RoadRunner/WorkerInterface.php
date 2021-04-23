<?php

declare(strict_types=1);

namespace Jellyfish\RoadRunner;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface WorkerInterface
{
    /**
     * @return \Psr\Http\Message\ServerRequestInterface|null
     */
    public function waitRequest(): ?ServerRequestInterface;

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Jellyfish\RoadRunner\WorkerInterface
     */
    public function respond(ResponseInterface $response): WorkerInterface;

    /**
     * @param string $error
     * @return \Jellyfish\RoadRunner\WorkerInterface
     */
    public function error(string $error): WorkerInterface;
}
