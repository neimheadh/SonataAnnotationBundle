<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\DependencyInjection;

use KunicMarko\SonataAnnotationBundle\Admin\AnnotationAdmin;
use KunicMarko\SonataAnnotationBundle\Tests\TestKernel;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Kernel configuration test suite.
 *
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class ConfigurationTest extends TestCase
{

    /**
     * Test kernel.
     *
     * @var Kernel|null
     */
    private ?Kernel $kernel = null;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->kernel = new TestKernel('test', false);
        $this->kernel->boot();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        if (file_exists($this->kernel->getCacheDir())) {
            $filesystem = new Filesystem();
            $filesystem->remove($this->kernel->getCacheDir());
        }
    }

    /**
     * Test admin.person service implementation is well-built.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldPersonAdminWellConfigured(): void
    {
        $this->assertInstanceOf(
          AnnotationAdmin::class,
          $this->kernel->getContainer()->get('admin.person'),
        );
    }

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
        $kernel = new TestKernel('test', true);
        $kernel->additionalConfigFiles[] = 'test/BadAdminModelClassParameter.config.yml';

        $e = null;
        try {
            $kernel->boot();
        } catch (LogicException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
          'Service "admin.bad" has a parameter "model.class.bad" as an argument but it is not found.',
          $e->getMessage()
        );
    }

}