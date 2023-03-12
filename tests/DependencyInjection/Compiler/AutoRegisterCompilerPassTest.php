<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use Neimheadh\SonataAnnotationBundle\Admin\AnnotationAdmin;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler\AutoRegisterCompilerPass;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\SonataAnnotationExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

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
     * Test an error is thrown if you define a bad PSR4 namespace for entities.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldThrowErrorOnInvalidNamespace(): void
    {
        $container = new ContainerBuilder();
        $container->set('annotation_reader', new AnnotationReader());
        $container->setParameter(
            SonataAnnotationExtension::PARAM_ENTITY_NAMESPACE,
            ['NonExisting\\Namespace'],
        );
        $register = new AutoRegisterCompilerPass();

        $e = null;
        try {
            $register->process($container);
        } catch (\LogicException $e) {}

        $this->assertNotNull($e);
        $this->assertEquals('Cannot find PS4 for namespace NonExisting\\Namespace', $e->getMessage());
    }
}