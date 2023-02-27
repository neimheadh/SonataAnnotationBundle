<?php

namespace KunicMarko\SonataAnnotationBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use KunicMarko\SonataAnnotationBundle\SonataAnnotationBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\BlockBundle\SonataBlockBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Sonata\Exporter\Bridge\Symfony\SonataExporterBundle;
use Sonata\Form\Bridge\Symfony\SonataFormBundle;
use Sonata\Twig\Bridge\Symfony\SonataTwigBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * Test suite kernel.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class TestKernel extends Kernel
{

    use MicroKernelTrait;

    /**
     * Additional configuration files to load.
     *
     * @var array<string>
     */
    public array $additionalConfigFiles = [];

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
          new MonologBundle(),
          new SecurityBundle(),
          new SonataAdminBundle(),
          new SonataAnnotationBundle(),
          new SonataBlockBundle(),
          new SonataDoctrineORMAdminBundle(),
          new SonataExporterBundle(),
          new SonataFormBundle(),
          new SonataTwigBundle(),
          new TwigBundle(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $configDir = __DIR__ . '/Resources/config';

        $loader->load(function (ContainerBuilder $container) use ($loader) {
            $container->setParameter('kernel.project_dir', __DIR__);
            $container->setParameter('kernel.cache_dir', dirname(__DIR__). '/var/cache');

            $container->register('kernel', self::class)
              ->addTag('controller.service_arguments')
              ->setAutoconfigured(true)
              ->setSynthetic(true)
              ->setPublic(true);

            $container->loadFromExtension('framework', [
              'router' => [
                'resource' => 'kernel::loadRoutes',
                'type' => 'service',
              ],
            ]);
            $container->getDefinition('kernel')->addTag('routing.route_loader');
        });

        $loader->load("$configDir/config.yml");

        if (substr(Kernel::VERSION, 0, 1) === '5') {
            $loader->load("$configDir/config_5.yml");
        } else {
            $loader->load("$configDir/config_latest.yml");
        }

        foreach ($this->additionalConfigFiles as $configFile) {
            if (substr($configFile, 0, 1) !== "/") {
                $configFile = "$configDir/$configFile";
            }

            $loader->load($configFile);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $configDir = __DIR__ . '/Resources/config';

        $routes->import("$configDir/routes.yaml");
    }

}