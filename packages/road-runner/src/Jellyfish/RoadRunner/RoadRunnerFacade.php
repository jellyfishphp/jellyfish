<?php

declare(strict_types=1);

namespace Jellyfish\RoadRunner;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RoadRunnerFacade implements RoadRunnerFacadeInterface
{
    /**
     * @var \Jellyfish\RoadRunner\RoadRunnerFactory
     */
    protected $factory;

    /**
     * @param \Jellyfish\RoadRunner\RoadRunnerFactory $factory
     */
    public function __construct(RoadRunnerFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface|null
     */
    public function waitRequest(): ?ServerRequestInterface
    {
        return $this->factory->getWorker()->waitRequest();
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Jellyfish\RoadRunner\RoadRunnerFacadeInterface
     */
    public function respond(ResponseInterface $response): RoadRunnerFacadeInterface
    {
        $this->factory->getWorker()->respond($response);

        return $this;
    }

    /**
     * @param string $error
     *
     * @return \Jellyfish\RoadRunner\RoadRunnerFacadeInterface
     */
    public function error(string $error): RoadRunnerFacadeInterface
    {
        $this->factory->getWorker()->error($error);

        return $this;
    }
}
