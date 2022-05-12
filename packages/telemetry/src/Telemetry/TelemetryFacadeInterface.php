<?php

namespace Jellyfish\Telemetry;

use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Context\Propagation\TextMapPropagatorInterface;

interface TelemetryFacadeInterface
{
    /**
     *
     * @return \OpenTelemetry\API\Trace\TracerInterface
     */
    public function createTracer(): TracerInterface;

    /**
     *
     * @return \OpenTelemetry\Context\Propagation\TextMapPropagatorInterface
     */
    public function createPropagator(): TextMapPropagatorInterface;
}
