<?php

namespace Jellyfish\Telemetry;

use Jellyfish\Config\ConfigFacadeInterface;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Aws\Xray\IdGenerator;
use OpenTelemetry\Aws\Xray\Propagator;
use OpenTelemetry\Context\Propagation\TextMapPropagatorInterface;
use OpenTelemetry\Contrib\OtlpGrpc\Exporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

class TelemetryFactory
{
    /**
     * @var \Jellyfish\Config\ConfigFacadeInterface
     */
    private ConfigFacadeInterface $configFacade;

    /**
     * @var \OpenTelemetry\API\Trace\TracerInterface|null
     */
    protected ?TracerInterface $tracer = null;

    /**
     * @param \Jellyfish\Config\ConfigFacadeInterface $configFacade
     */
    public function __construct(ConfigFacadeInterface $configFacade)
    {
        $this->configFacade = $configFacade;
    }

    /**
     *
     * @return \OpenTelemetry\API\Trace\TracerInterface
     * @throws \Jellyfish\Config\Exception\ConfigKeyNotFoundException
     * @throws \Jellyfish\Config\Exception\NotSupportedConfigValueTypeException
     */
    public function createTracer(): TracerInterface
    {
        if ($this->tracer === null) {
            $exporter = new Exporter($this->configFacade->get(TelemetryConstants::OTEL_ENDPOINT));
            $this->tracer = (new TracerProvider(new SimpleSpanProcessor($exporter), null, null, null, new IdGenerator()))->getTracer();
        }

        return $this->tracer;
    }

    /**
     *
     * @return \OpenTelemetry\Context\Propagation\TextMapPropagatorInterface
     */
    public function createPropagator(): TextMapPropagatorInterface
    {
        return new Propagator();
    }
}
