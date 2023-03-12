<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\DependencyInjection;

use Exception;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\SonataAnnotationExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * SonataAnnotationExtension test suite.
 */
class SonataAnnotationExtensionTest extends TestCase
{

    /**
     * Test deprecated configuration is handled.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldHaveDeprecationHandled(): void
    {
        $extension = new SonataAnnotationExtension();
        $container = new ContainerBuilder();

        $extension->load([
            'sonata_annotation' => [
                'directory' => __DIR__,
            ]
        ], $container);

        $this->assertEquals([
            'Neimheadh\SonataAnnotationBundle\Tests\DependencyInjection'
        ], $container->getParameter(
            $extension::PARAM_ENTITY_NAMESPACE
        ));

        // Test with empty directory
        if (!is_dir(__DIR__ . '/delete.me')) {
            mkdir(__DIR__ . '/delete.me');
        }
        $extension->load([
            'sonata_annotation' => [
                'directory' => __DIR__ . '/delete.me',
            ]
        ], $container);
        rmdir(__DIR__ . '/delete.me');

        $this->assertEquals([], $container->getParameter(
            $extension::PARAM_ENTITY_NAMESPACE
        ));
    }

    /**
     * Test the slash is added to the end of the configured entity namespace.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldAddNamespaceSlash(): void
    {
        $extension = new SonataAnnotationExtension();
        $container = new ContainerBuilder();

        $extension->load([
            'sonata_annotation' => [
                'entity' => [
                    'namespace' => 'App\\Entity\\'
                ]
            ]
        ], $container);

        $this->assertEquals(
            ['App\\Entity\\'],
            $container->getParameter($extension::PARAM_ENTITY_NAMESPACE)
        );

        $extension->load([
            'sonata_annotation' => [
                'entity' => [
                    'namespace' => 'App\\Entity'
                ]
            ]
        ], $container);

        $this->assertEquals(
            ['App\\Entity\\'],
            $container->getParameter($extension::PARAM_ENTITY_NAMESPACE)
        );
    }
}