<?php

declare(strict_types=1);

namespace Jellyfish\Telemetry;

use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Context\Propagation\TextMapPropagatorInterface;

class TelemetryFacade implements TelemetryFacadeInterface
{
    /**
     * @var \Jellyfish\Telemetry\TelemetryFactory
     */
    protected TelemetryFactory $telemetryFactory;

    /**
     * @param \Jellyfish\Telemetry\TelemetryFactory $telemetryFactory
     */
    public function __construct(TelemetryFactory $telemetryFactory)
    {
        $this->telemetryFactory = $telemetryFactory;
    }

    /**
     *
     * @return \OpenTelemetry\API\Trace\TracerInterface
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     */
    public function createTracer(): TracerInterface
    {
        return $this->telemetryFactory->createTracer();
    }

    /**
     *
     * @return \OpenTelemetry\Context\Propagation\TextMapPropagatorInterface
     */
    public function createPropagator(): TextMapPropagatorInterface
    {
        return $this->telemetryFactory->createPropagator();
    }
}
