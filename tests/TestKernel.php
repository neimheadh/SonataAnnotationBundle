<?php

namespace KunicMarko\SonataAnnotationBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use KunicMarko\SonataAnnotationBundle\SonataAnnotationBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Test suite kernel.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
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
          new FrameworkBundle(),
          new KnpMenuBundle(),
          new SecurityBundle(),
          new SonataAdminBundle(),
          new SonataAnnotationBundle(),
          new SonataDoctrineORMAdminBundle(),
          new TwigBundle(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.yml');

        if (str_starts_with(Kernel::VERSION, '5')) {
            $loader->load(__DIR__ . '/config_5.yml');
        } else {
            $loader->load(__DIR__ . '/config_latest.yml');
        }

        $loader->load(function (ContainerBuilder $container) use ($loader) {
            $container->setParameter('kernel.project_dir', __DIR__);

            $container->register('kernel', self::class)
              ->addTag('controller.service_arguments')
              ->setAutoconfigured(true)
              ->setSynthetic(true)
              ->setPublic(true);
        });
    }

}