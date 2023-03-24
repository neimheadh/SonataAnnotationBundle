<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Reader;

use Exception;
use Neimheadh\SonataAnnotationBundle\Admin\AnnotationAdmin;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\AddRoute;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\RemoveRoute;
use Neimheadh\SonataAnnotationBundle\AnnotationReader;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use Neimheadh\SonataAnnotationBundle\Reader\RouteReader;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\ArgumentAnnotation\ArgumentAnnotation;
use ReflectionClass;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;

/**
 * RouteReader test suite.
 */
class RouteReaderTest extends KernelTestCase
{

    /**
     * Test the reader support AddRoute & RemoveRoute annotation.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldSupportAnnotation(): void
    {
        $reader = new RouteReader(new AnnotationReader());

        $routes = $reader->getRoutes(
            new ReflectionClass(RouteReaderTestCase::class)
        );

        $added = new AddRoute();
        $removed = new RemoveRoute();

        $added->name = 'custom_route';
        $added->path = '/custom/route';
        $removed->name = 'app_show';

        $this->assertCount(2, $routes);
        $this->assertCount(1, $routes[0]);
        $this->assertCount(1, $routes[1]);
        $this->assertEquals(
            ['custom_route' => $added],
            $routes[0]
        );
        $this->assertEquals(
            ['app_show' => $removed],
            $routes[1]
        );
    }

    /**
     * Test the route name is mandatory.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldRouteNameMandatory(): void
    {
        $reader = new RouteReader(new AnnotationReader());

        $e = null;
        try {
            $reader->getRoutes(
                new ReflectionClass(NoNameAddRouteReaderTestCase::class)
            );
        } catch (MissingAnnotationArgumentException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
            sprintf(
                'Argument "%s" is mandatory for annotation %s on %s.',
                'name',
                AddRoute::class,
                NoNameAddRouteReaderTestCase::class,
            ),
            $e->getMessage(),
        );

        $e = null;
        try {
            $reader->getRoutes(
                new ReflectionClass(NoNameRemoveRouteReaderTestCase::class)
            );
        } catch (MissingAnnotationArgumentException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
            sprintf(
                'Argument "%s" is mandatory for annotation %s on %s.',
                'name',
                RemoveRoute::class,
                NoNameRemoveRouteReaderTestCase::class,
            ),
            $e->getMessage(),
        );
    }

    /**
     * Test the reader modify admin routes.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldModifyRoutes(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var AnnotationAdmin $admin */
        $admin = $container->get('app.admin.Book');
        /** @var RouteCollection $routes */
        $routes = $admin->getRoutes();

        $this->assertTrue($routes->has('custom'));
        $this->assertFalse($routes->has('batch'));
    }

    /**
     * Test the argument system is handled.
     *
     * @test
     * @functionnal
     *
     * @return void
     * @throws Exception
     */
    public function shouldHandlePHP8Arguments(): void
    {
        if (!class_exists('ReflectionArgument')) {
            $this->assertTrue(true);
        }
        $class = new ReflectionClass(ArgumentAnnotation::class);
        $reader = new RouteReader(new AnnotationReader());

        $routes = $reader->getRoutes(
            $class,
        );

        $this->assertEquals(
            [
                ['test' => new AddRoute('test', '/test')],
                ['list' => new RemoveRoute('list')],
            ],
            $routes
        );
    }
}

/**
 * @AddRoute(name="custom_route", path="/custom/route")
 * @RemoveRoute(name="app_show")
 */
class RouteReaderTestCase
{

}

/**
 * @AddRoute()
 */
class NoNameAddRouteReaderTestCase
{

}

/**
 * @RemoveRoute()
 */
class NoNameRemoveRouteReaderTestCase
{

}