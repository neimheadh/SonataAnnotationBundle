<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Reader;

use Neimheadh\SonataAnnotationBundle\AnnotationReader;
use Exception;
use Neimheadh\SonataAnnotationBundle\Annotation\DashboardAction;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use Neimheadh\SonataAnnotationBundle\Reader\DashboardActionReader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * DashboardActionReader test suite.
 */
class DashboardActionReaderTest extends TestCase
{

    /**
     * Test the reader support the annotation.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldSupportAnnotation(): void
    {
        $reader = new DashboardActionReader(new AnnotationReader());

        $actions = $reader->getActions(
          new ReflectionClass(DashboardActionReaderTestCase::class),
          []
        );
        $this->assertCount(1, $actions);
        $this->assertEquals(['template' => 'test.html.twig'],
                            current($actions));
    }

    /**
     * Test the template is mandatory.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldTemplateAttributeMandatory(): void
    {
        $reader = new DashboardActionReader(new AnnotationReader());

        $e = null;
        try {
            $reader->getActions(
              new ReflectionClass(
                NoTemplateDashboardActionReaderTestCase::class
              ),
              []
            );
        } catch (MissingAnnotationArgumentException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
          sprintf(
            'Argument "%s" is mandatory for annotation %s on %s.',
            'template',
            DashboardAction::class,
            NoTemplateDashboardActionReaderTestCase::class,
          ),
          $e->getMessage(),
        );
    }

}


/**
 * @DashboardAction(template="test.html.twig")
 */
class DashboardActionReaderTestCase
{

}

/**
 * @DashboardAction()
 */
class NoTemplateDashboardActionReaderTestCase
{

}