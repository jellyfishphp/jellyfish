<?php

namespace Jellyfish\MailSwiftmailer;

use Jellyfish\Config\ConfigConstants;
use Jellyfish\Mail\MailConstants;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MailSwiftmailerServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $container
     *
     * @return void
     */
    public function register(Container $container): void
    {
        $this->registerMailFacade($container);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Jellyfish\MailSwiftmailer\MailSwiftmailerServiceProvider
     */
    protected function registerMailFacade(Container $container): MailSwiftmailerServiceProvider
    {
        $container->offsetSet(MailConstants::FACADE, static function (Container $container) {
            $factory = new MailSwiftmailerFactory(
                $container->offsetGet(ConfigConstants::FACADE)
            );

            return new MailSwiftmailerFacade($factory);
        });

        return $this;
    }
}
