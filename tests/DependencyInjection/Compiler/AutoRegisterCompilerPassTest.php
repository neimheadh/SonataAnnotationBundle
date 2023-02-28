<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\DependencyInjection\Compiler;

use Neimheadh\SonataAnnotationBundle\Admin\AnnotationAdmin;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler\AutoRegisterCompilerPass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Throwable;

/**
 * Dependency injection auto-register test suite
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class AutoRegisterCompilerPassTest extends KernelTestCase
{

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->bootKernel();
    }

    /**
     * Test the book admin is created.
     *
     * @test
     * @functional
     */
    public function shouldBookAdminCreated(): void
    {
        $this->assertInstanceOf(
            AnnotationAdmin::class,
            static::getContainer()->get('app.admin.Book')
        );
    }

    /**
     * Test model without namespace admin are not created.
     *
     * @test
     * @functional
     */
    public function shouldNotCreateWithoutNamespace(): void
    {
        $e = null;

        try {
            static::getContainer()->get('app.admin.NoNamespace');
        } catch (ServiceNotFoundException $e) {
        }

        $this->assertNotNull($e);
    }

    /**
     * Test model with bad class name admin are not created.
     *
     * @test
     * @functional
     */
    public function shouldNotCreateWithWrongClassName(): void
    {
        $e = null;
        try {
            static::getContainer()->get('app.admin.WrongClassName');
        } catch (ServiceNotFoundException $e) {
        }
        $this->assertNotNull($e);

        $e = null;
        try {
            static::getContainer()->get('app.admin.IHaveABadClassName');
        } catch (ServiceNotFoundException $e) {
        }
        $this->assertNotNull($e);
    }

    /**
     * Test no exception send when entity directory does not exist.
     *
     * @test
     * @functional
     */
    public function shouldNotThrowExceptionWhenEntityDirectoryDoesNotExist(
    ): void {
        $container = new ContainerBuilder();

        $container->setParameter('sonata_annotation.directory', 'unknown');
        $container->set('annotation_reader', static::getContainer()->get('annotation_reader'));

        $compiler = new AutoRegisterCompilerPass();
        $e = null;

        try {
            $compiler->process($container);
        } catch (Throwable $e) {}
        $this->assertNull($e);
    }

}