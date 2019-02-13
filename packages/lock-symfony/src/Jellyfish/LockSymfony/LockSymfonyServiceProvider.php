<?php

namespace Jellyfish\LockSymfony;

use Jellyfish\Lock\LockIdentifierGeneratorInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Lock\Factory as SymfonyLockFactory;
use Symfony\Component\Lock\Store\RedisStore;

class LockSymfonyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $pimple
     *
     * @return void
     */
    public function register(Container $pimple): void
    {
        $this->createLockFactory($pimple);
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Pimple\ServiceProviderInterface
     */
    protected function createLockFactory(Container $container): ServiceProviderInterface
    {
        $self = $this;

        $container->offsetSet('lock_factory', function (Container $container) use ($self) {
            return new LockFactory(
                $self->createSymfonyLockFactory($container),
                $self->createLockIdentifierGenerator()
            );
        });

        return $this;
    }

    /**
     * @param \Pimple\Container $container
     *
     * @return \Symfony\Component\Lock\Factory
     */
    protected function createSymfonyLockFactory(Container $container): SymfonyLockFactory
    {
        $redisStore = new RedisStore($container->offsetGet('redis_client'));

        return new SymfonyLockFactory($redisStore);
    }

    /**
     * @return \Jellyfish\Lock\LockIdentifierGeneratorInterface
     */
    protected function createLockIdentifierGenerator(): LockIdentifierGeneratorInterface
    {
        return new LockIdentifierGenerator();
    }
}
