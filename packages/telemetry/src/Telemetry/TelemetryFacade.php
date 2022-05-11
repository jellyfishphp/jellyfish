<?php

namespace Jellyfish\Telemetry;

use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Context\Propagation\TextMapPropagatorInterface;

class TelemetryFacade implements TelemetryFacadeInterface
{
    /**
     * @var \Jellyfish\Telemetry\TelemetryFactory
     */
    protected TelemetryFactory $otelFactory;

    /**
     * @param \Jellyfish\Telemetry\TelemetryFactory $otelFactory
     */
    public function __construct(TelemetryFactory $otelFactory)
    {
        $this->otelFactory = $otelFactory;
    }

    /**
     *
     * @return \OpenTelemetry\API\Trace\TracerInterface
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     */
    public function createTracer(): TracerInterface
    {
        return $this->otelFactory->createTracer();
    }

    /**
     *
     * @return \OpenTelemetry\Context\Propagation\TextMapPropagatorInterface
     */
    public function createPropagator(): TextMapPropagatorInterface
    {
        return $this->otelFactory->createPropagator();
    }
}
