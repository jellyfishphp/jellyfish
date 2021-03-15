<?php

declare(strict_types=1);

namespace Jellyfish\HttpAuthentication;

use Jellyfish\Http\HttpConstants;
use Jellyfish\Http\HttpFacadeInterface;
use Jellyfish\HttpAuthentication\Middleware\AuthenticationMiddleware;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class HttpAuthenticationServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerHttpAuthenticationFacade($container)
            ->registerMiddleware($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\HttpAuthentication\HttpAuthenticationServiceProvider
     */
    protected function registerHttpAuthenticationFacade(Container $container): HttpAuthenticationServiceProvider
    {
        $container->offsetSet(HttpAuthenticationConstants::FACADE, static function (Container $container) {
            $httpAuthenticationFactory = new HttpAuthenticationFactory($container->offsetGet('app_dir'));

            return new HttpAuthenticationFacade($httpAuthenticationFactory);
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\HttpAuthentication\HttpAuthenticationServiceProvider
     */
    protected function registerMiddleware(Container $container): HttpAuthenticationServiceProvider
    {
        $container->extend(
            HttpConstants::FACADE,
            static function (HttpFacadeInterface $httpFacade, Container $container) {
                $authenticationMiddleware = new AuthenticationMiddleware(
                    $container->offsetGet(HttpAuthenticationConstants::FACADE)
                );

                return $httpFacade->addMiddleware($authenticationMiddleware);
            }
        );

        return $this;
    }
}
