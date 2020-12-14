<?php

/**
 * @see       https://github.com/laminas/laminas-di for the canonical source repository
 * @copyright https://github.com/laminas/laminas-di/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-di/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Di\Container;

use Laminas\Di\ConfigInterface;
use Laminas\Di\Injector;
use Laminas\Di\InjectorInterface;
use Psr\Container\ContainerInterface;

/**
 * Implements the DependencyInjector service factory for laminas-servicemanager
 */
class InjectorFactory
{
    /**
     * @param ContainerInterface $container
     * @return ConfigInterface
     */
    private function createConfig(ContainerInterface $container) : ConfigInterface
    {
        if ($container->has(ConfigInterface::class)) {
            return $container->get(ConfigInterface::class);
        }

        if ($container->has(\Zend\Di\ConfigInterface::class)) {
            return $container->get(\Zend\Di\ConfigInterface::class);
        }

        return (new ConfigFactory())->create($container);
    }

    /**
     * {@inheritDoc}
     */
    public function create(ContainerInterface $container) : InjectorInterface
    {
        $config = $this->createConfig($container);
        return new Injector($config, $container);
    }

    /**
     * Make the instance invokable
     */
    public function __invoke(ContainerInterface $container) : InjectorInterface
    {
        return $this->create($container);
    }
}
