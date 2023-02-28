<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\DependencyInjection\Compiler;

use Neimheadh\SonataAnnotationBundle\Annotation\Access;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use Neimheadh\SonataAnnotationBundle\Tests\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;

/**
 * Access compiler pass test suite.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class AccessCompilerPassTest extends KernelTestCase
{

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