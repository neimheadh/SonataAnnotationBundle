<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\DependencyInjection\Compiler;

use InvalidArgumentException;
use KunicMarko\SonataAnnotationBundle\Admin\AnnotationAdmin;
use KunicMarko\SonataAnnotationBundle\Tests\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Component\Routing\RouterInterface;

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
     * Test the compiler should throw an exception if we set a class which is
     * not an administrated model.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldThrowExceptionOnBadAdminClass(): void
    {
        $kernel = new TestKernel('test', false);

        $model = __DIR__.'/../../Resources/Model/BadChildAdminClass.php.dist';
        $file = __DIR__.'/../../Resources/Model/BadChildAdminClass.php';

        if (is_file($file)) {
            unlink($file);
        }

        copy($model, $file);
        $e = null;
        try {
            $kernel->boot();
        } catch (InvalidArgumentException $e) {}
        unlink($file);

        $this->assertNotNull($e);
        $this->assertEquals('Unknown is missing Admin Class.', $e->getMessage());
    }
}