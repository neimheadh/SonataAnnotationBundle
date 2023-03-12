<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * SonataAnnotationBundle extension.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class SonataAnnotationExtension extends Extension
{

    public const PARAM_ENTITY_NAMESPACE = 'sonata_annotation.config.entity.namespace';

    public const PARAM_MENU_USE_NAMESPACE = 'sonata_annotation.config.namespace_as_group';

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->loadEntityConfiguration(
            $config['entity'],
            $container,
            $config['directory'] ?? null,
        );
        $this->loadMenuConfiguration($config['menu'], $container);
        $this->loadReaderServices($container);
    }

    /**
     * Get the namespace of the given directory.
     *
     * @param string $directory The directory.
     *
     * @return string|null
     */
    private function getDirectoryNamespace(string $directory): ?string
    {
        $files = Finder::create()
            ->in($directory)
            ->files()
            ->name('*.php');
        $pattern = '/^namespace\s+(.+);/';

        foreach ($files as $file) {
            $namespaceLine = current(
                preg_grep($pattern, file($file->getPathname()))
            );

            if ($namespaceLine) {
                preg_match($pattern, $namespaceLine, $matches);
                $namespace = $matches[1];
                $subdir = substr($file->getPath(), strlen($directory));
                $subNs = str_replace('/', '\\\\', $subdir);
                return preg_replace("/\\$subNs$/", '', $namespace);
            }
        }

        return null;
    }

    /**
     * Load bundle entity configuration.
     *
     * @param array            $config    Entity configuration.
     * @param ContainerBuilder $container Container builder.
     * @param string|null      $directory Deprecated directory configuration.
     *
     * @return void
     */
    private function loadEntityConfiguration(
        array $config,
        ContainerBuilder $container,
        ?string $directory
    ): void {
        if ($directory !== null) {
            $ns = $this->getDirectoryNamespace($directory);
            $container->setParameter(
                self::PARAM_ENTITY_NAMESPACE,
                $ns ? [$ns] : []
            );
        } else {
            $container->setParameter(
                self::PARAM_ENTITY_NAMESPACE,
                array_map(
                    fn(string $ns) => str_ends_with($ns, '\\')
                        ? $ns
                        : sprintf('%s\\', $ns),
                    $config['namespace']
                )
            );
        }
    }

    /**
     * Load bundle menu configuration.
     *
     * @param array            $config    Menu configuration.
     * @param ContainerBuilder $container Container builder.
     *
     * @return void
     */
    private function loadMenuConfiguration(
        array $config,
        ContainerBuilder $container
    ): void {
        $container->setParameter(
            self::PARAM_MENU_USE_NAMESPACE,
            $config['namespace_as_group']
        );
    }

    /**
     * Load annotation readers.
     *
     * @param ContainerBuilder $container Container builder.
     *
     * @return void
     * @throws Exception
     */
    private function loadReaderServices(ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(
                __DIR__ . '/../Resources/config'
            )
        );
        $loader->load('reader.xml');
    }

}
