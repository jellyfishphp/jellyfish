<?php

declare(strict_types=1);

namespace Jellyfish\RoadRunner;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RoadRunnerFacadeInterface
{
    /**
     * @return \Psr\Http\Message\ServerRequestInterface|null
     */
    public function waitRequest(): ?ServerRequestInterface;

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Jellyfish\RoadRunner\RoadRunnerFacadeInterface
     */
    public function respond(ResponseInterface $response): RoadRunnerFacadeInterface;

    /**
     * @param string $error
     *
     * @return \Jellyfish\RoadRunner\RoadRunnerFacadeInterface
     */
    public function error(string $error): RoadRunnerFacadeInterface;
}
