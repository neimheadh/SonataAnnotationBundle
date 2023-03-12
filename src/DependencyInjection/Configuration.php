<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Marko Kunic <kunicmarko20@gmail.com>
 */
final class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sonata_annotation');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('entity')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('namespace')
                            ->defaultValue(['App\\Entity\\'])
                            ->beforeNormalization()
                                ->castToArray()
                            ->end()
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('menu')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('namespace_as_group')
                            ->defaultValue(true)
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('directory')
                    ->setDeprecated(
                        'neimheadh/sonata-annotation-bundle',
                        '2.0.5'
                    )
                ->end()
            ->end();

        return $treeBuilder;
    }
}
