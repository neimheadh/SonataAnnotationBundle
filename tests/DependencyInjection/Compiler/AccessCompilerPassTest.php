<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\DependencyInjection\Compiler;

use KunicMarko\SonataAnnotationBundle\Annotation\Access;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use KunicMarko\SonataAnnotationBundle\Tests\TestKernel;
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
            'KunicMarko\\SonataAnnotationBundle\\Tests\\Resources\\Model\\BadAccessAdminClass'
          ),
          $e->getMessage()
        );
    }
}