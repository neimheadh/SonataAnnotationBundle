<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Reader;

use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use KunicMarko\SonataAnnotationBundle\Annotation\DashboardAction;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use KunicMarko\SonataAnnotationBundle\Reader\DashboardActionReader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

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