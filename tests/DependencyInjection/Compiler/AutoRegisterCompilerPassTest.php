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
     * Test the book admin is created.
     *
     * @test
     * @functional
     */
    public function shouldBookAdminCreated(): void
    {
        $admin = static::getContainer()->get('app.admin.Book');

        $this->assertInstanceOf(AnnotationAdmin::class, $admin);

        $this->assertEquals('Book admin', $admin->getLabel());
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
        } catch (\LogicException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
            'Cannot find PS4 for namespace NonExisting\\Namespace',
            $e->getMessage()
        );
    }

    /**
     * Test Admin argument admin is created.
     *
     * @test
     * @function
     *
     * @return void
     */
    public function shouldAddAdminArgument(): void
    {
        if (!class_exists('ReflectionAttribute')) {
            $this->assertTrue(true);
            return;
        }

        $compiler = new AutoRegisterCompilerPass();
        $container = new ContainerBuilder();

        $container->setParameter(
            SonataAnnotationExtension::PARAM_ENTITY_NAMESPACE,
            ['Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\ArgumentAnnotation']
        );
        $container->setParameter(
            SonataAnnotationExtension::PARAM_MENU_USE_NAMESPACE,
            true
        );

        $services = [
            'sonata.annotation.reader.action_button',
            'sonata.annotation.reader.datagrid',
            'sonata.annotation.reader.datagrid_values',
            'sonata.annotation.reader.dashboard_action',
            'sonata.annotation.reader.export',
            'sonata.annotation.reader.form',
            'sonata.annotation.reader.list',
            'sonata.annotation.reader.route',
            'sonata.annotation.reader.show',
        ];
        foreach ($services as $service) {
            $container->set(
                $service,
                static::getContainer()->get(
                    $service
                )
            );
        }

        $compiler->process($container);

        $this->assertEquals(
            array_values(
                array_merge(
                    ['service_container', 'app.admin.ArgumentAnnotation'],
                    $services
                ),
            ),
            array_values($container->getServiceIds())
        );

        $admin = $container->getDefinition('app.admin.ArgumentAnnotation');
        $tag = current($admin->getTag('sonata.admin'));

        $this->assertEquals('Test', $tag['label']);
    }

    /**
     * Test bad class name and namespace are handled.
     *
     * @test
     * @functionnal
     *
     * @return void
     * @throws \Exception
     */
    public function shouldHandleWrongClass(): void
    {
        $compiler = new AutoRegisterCompilerPass();

        $container = new ContainerBuilder();
        $container->setParameter(
            SonataAnnotationExtension::PARAM_ENTITY_NAMESPACE,
            ['Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\WrongClassname']
        );

        $compiler->process($container);
        $this->assertEquals(['service_container'], $container->getServiceIds());
    }

}