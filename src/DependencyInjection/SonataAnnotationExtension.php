<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * SonataAnnotationBundle extension.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class SonataAnnotationExtension extends Extension
{

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'sonata_annotation.directory',
            $config['directory'] ?? $container->getParameter(
                'kernel.project_dir'
            ) . '/src/'
        );

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(
                __DIR__ . '/../Resources/config'
            )
        );
        $loader->load('reader.xml');
    }

}
