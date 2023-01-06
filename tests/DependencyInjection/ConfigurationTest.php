<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\DependencyInjection;

use KunicMarko\SonataAnnotationBundle\Admin\AnnotationAdmin;
use Symfony\Component\DependencyInjection\Container;
use KunicMarko\SonataAnnotationBundle\Tests\TestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Kernel configuration test suite.
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
     * Test admin.person service implementation is well-built (with reader
     * dependencies).
     *
     * @test
     * @functional
     */
    public function shouldHaveFormReader(): void
    {
        $this->assertInstanceOf(
          AnnotationAdmin::class,
          $this->kernel->getContainer()->get('admin.person'),
        );
    }

}