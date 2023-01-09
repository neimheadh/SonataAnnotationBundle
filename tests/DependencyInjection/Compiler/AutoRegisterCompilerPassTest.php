<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\DependencyInjection\Compiler;

use KunicMarko\SonataAnnotationBundle\Admin\AnnotationAdmin;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Dependency injection auto-register test suite.
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

}