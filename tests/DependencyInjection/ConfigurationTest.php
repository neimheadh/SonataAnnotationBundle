<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\DependencyInjection;

use Neimheadh\SonataAnnotationBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * Kernel configuration test suite.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class ConfigurationTest extends TestCase
{

    /**
     * Configuration should have default values.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldHasDefaultValues(): void
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $config = $processor->processConfiguration($configuration, []);

        $this->assertArrayHasKey('entity', $config);
        $this->assertArrayHasKey('menu', $config);
        $this->assertArrayHasKey('namespace', $config['entity']);
        $this->assertArrayHasKey('namespace_as_group', $config['menu']);

        $this->assertEquals(['App\\Entity\\'], $config['entity']['namespace']);
        $this->assertTrue($config['menu']['namespace_as_group']);

        $this->assertArrayNotHasKey('directory', $config);
    }

}