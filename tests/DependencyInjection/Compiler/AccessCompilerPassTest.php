<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\DependencyInjection\Compiler;

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
}