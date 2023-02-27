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
                ->scalarNode('directory')->end()
            ->end();

        return $treeBuilder;
    }
}
