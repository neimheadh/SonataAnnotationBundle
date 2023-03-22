<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use LogicException;
use Neimheadh\SonataAnnotationBundle\Admin\AnnotationAdmin;
use Neimheadh\SonataAnnotationBundle\Annotation\Access;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler\AccessCompilerPass;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler\AutoRegisterCompilerPass;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\SonataAnnotationExtension;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\AccessMissingRole\AccessMissingRole;
use ReflectionException;
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
     * Test access annotation has role mandatory.
     *
     * @test
     * @functionnal
     *
     * @return void
     * @throws ReflectionException
     * @throws Exception
     */
    public function shouldHaveRoleMandatory(): void
    {
        $container = new ContainerBuilder();
        $accessCompiler = new AccessCompilerPass();

        $container->setParameter(
            SonataAnnotationExtension::PARAM_ENTITY_NAMESPACE,
            ['Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\AccessMissingRole']
        );
        $container->setParameter(
            SonataAnnotationExtension::PARAM_MENU_USE_NAMESPACE,
            true
        );
        $container->setParameter(
            'security.role_hierarchy.roles',
            static::getContainer()->getParameter(
                'security.role_hierarchy.roles'
            )
        );

        (new AutoRegisterCompilerPass())->process($container);

        $e = null;
        try {
            $accessCompiler->process($container);
        } catch (MissingAnnotationArgumentException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
            sprintf(
                'Argument "role" is mandatory for annotation %s on %s.',
                Access::class,
                AccessMissingRole::class
            ),
            $e->getMessage()
        );
    }

    /**
     * Test Access annotation can be used has argument.
     *
     * @test
     * @functionnal
     *
     * @return void
     * @throws Exception
     */
    public function shouldHandleAccessArgument(): void
    {
        $container = new ContainerBuilder();
        $accessCompiler = new AccessCompilerPass();

        $container->setParameter(
            SonataAnnotationExtension::PARAM_ENTITY_NAMESPACE,
            ['Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\ArgumentAnnotation']
        );
        $container->setParameter(
            SonataAnnotationExtension::PARAM_MENU_USE_NAMESPACE,
            true
        );
        $container->setParameter(
            'security.role_hierarchy.roles',
            [],
        );

        (new AutoRegisterCompilerPass())->process($container);
        $accessCompiler->process($container);

        $this->assertEquals(
            [
                'ROLE_ADMIN' => [
                    'ROLE_APP_ADMIN_ARGUMENTANNOTATION_LIST',
                    'ROLE_APP_ADMIN_ARGUMENTANNOTATION_VIEW',
                    'ROLE_APP_ADMIN_ARGUMENTANNOTATION_EXPORT',
                ],
            ],
            $container->getParameter('security.role_hierarchy.roles')
        );
    }

}