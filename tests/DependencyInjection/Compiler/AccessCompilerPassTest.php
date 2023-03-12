<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use LogicException;
use Neimheadh\SonataAnnotationBundle\Admin\AnnotationAdmin;
use Neimheadh\SonataAnnotationBundle\Annotation\Access;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler\AccessCompilerPass;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use Neimheadh\SonataAnnotationBundle\Tests\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Access compiler pass test suite.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class AccessCompilerPassTest extends KernelTestCase
{

    /**
     * Test a wrongly configured with parameter admin class throw a logic
     * exception.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldBadAdminClassNameThrowLogicException(): void
    {
        $accessCompiler = new AccessCompilerPass();
        $container = new ContainerBuilder();

        $container->set('annotation_reader', new AnnotationReader());
        $container->setParameter('security.role_hierarchy.roles', []);

        $definition = new Definition();
        $definition->setClass(AnnotationAdmin::class);
        $definition->addTag(
            'sonata.admin', [
                'manager_type' => 'orm',
                'model_class' => '%%model.class.bad%%',
            ]
        );
        $container->setDefinition('admin.bad', $definition);

        $e = null;
        try {
            $accessCompiler->process($container);
        } catch (LogicException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
            'Service "admin.bad" has a parameter "model.class.bad" as an argument but it is not found.',
            $e->getMessage()
        );
    }


    /**
     * Test book admin permission roles.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldContainerHaveBookPermissionRoles(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();

        $this->assertSame(
            ['ROLE_USER' => ['ROLE_APP_ADMIN_BOOK_READ']],
            $container->getParameter('security.role_hierarchy.roles'),
        );
    }

    /**
     * Test the compiler should throw an exception if we don't set the access
     * annotation role.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldThrowExceptionOnBadAccessClass(): void
    {
        $kernel = new TestKernel('test', false);

        $model = __DIR__ . '/../../Resources/Model/BadAccessAdminClass.php.dist';
        $file = __DIR__ . '/../../Resources/Model/BadAccessAdminClass.php';

        if (is_file($file)) {
            unlink($file);
        }

        copy($model, $file);
        $e = null;
        try {
            $kernel->boot();
        } catch (MissingAnnotationArgumentException $e) {
        }
        unlink($file);

        $this->assertNotNull($e);
        $this->assertEquals(
            sprintf(
                'Argument "role" is mandatory for annotation %s on %s.',
                Access::class,
                'Neimheadh\\SonataAnnotationBundle\\Tests\\Resources\\Model\\BadAccessAdminClass'
            ),
            $e->getMessage()
        );
    }

}