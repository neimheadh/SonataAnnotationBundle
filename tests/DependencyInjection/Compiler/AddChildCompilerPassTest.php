<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\DependencyInjection\Compiler;

use Exception;
use InvalidArgumentException;
use Neimheadh\SonataAnnotationBundle\Admin\AnnotationAdmin;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler\AddChildCompilerPass;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler\AutoRegisterCompilerPass;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\SonataAnnotationExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add child annotation compiler pass test suite.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class AddChildCompilerPassTest extends KernelTestCase
{

    /**
     * Test author admin has book as children.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldAuthorHasBookChildren(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();

        /** @var AnnotationAdmin $admin */
        $admin = $container->get('app.admin.Author');
        $children = $admin->getChildren();

        $this->assertArrayHasKey('app.admin.Book', $children);
    }

    /**
     * Test thrown error when a child admin is misconfigured.
     *
     * @test
     * @functionnal
     *
     * @return void
     * @throws Exception
     */
    public function shouldThrowErrorOnBadChildAdmin(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter(
            SonataAnnotationExtension::PARAM_ENTITY_NAMESPACE,
            ['Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\BadChildAdmin']
        );
        $container->setParameter(
            SonataAnnotationExtension::PARAM_MENU_USE_NAMESPACE,
            true
        );

        $services = [
            'sonata.annotation.reader.add_child',
        ];
        foreach ($services as $service) {
            $container->set(
                $service,
                static::getContainer()->get(
                    $service
                )
            );
        }

        (new AutoRegisterCompilerPass())->process($container);

        $compiler = new AddChildCompilerPass();

        $e = null;
        try {
            $compiler->process($container);
        } catch (InvalidArgumentException $e) {}

        $this->assertNotNull($e);
        $this->assertEquals('WrongOne is missing admin class.', $e->getMessage());
    }

}