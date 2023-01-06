<?php

namespace KunicMarko\SonataAnnotationBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use KunicMarko\SonataAnnotationBundle\SonataAnnotationBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Test suite kernel.
 */
class TestKernel extends Kernel
{

    /**
     * {@inheritDoc}
     */
    public function getCacheDir(): string
    {
        return __DIR__ . '/../var/cache/' . $this->getEnvironment();
    }
    /**
     * {@inheritDoc}
     */
    public function registerBundles(): iterable
    {
        return [
          new DoctrineBundle(),
          new SonataAnnotationBundle(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.yml');

        $loader->load(function (ContainerBuilder $container) use ($loader) {
            $container->register('kernel', self::class)
              ->addTag('controller.service_arguments')
              ->setAutoconfigured(true)
              ->setSynthetic(true)
              ->setPublic(true);
        });
    }

}