<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\DependencyInjection\Compiler;

use KunicMarko\SonataAnnotationBundle\Admin\AnnotationAdmin;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

}