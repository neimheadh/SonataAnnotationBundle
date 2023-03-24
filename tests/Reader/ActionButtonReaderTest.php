<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Reader;

use Exception;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ActionButton;
use Neimheadh\SonataAnnotationBundle\AnnotationReader;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use Neimheadh\SonataAnnotationBundle\Reader\ActionButtonReader;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\ArgumentAnnotation\ArgumentAnnotation;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * ActionButtonReader test suite.
 */
class ActionButtonReaderTest extends TestCase
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
        $reader = new ActionButtonReader(new AnnotationReader());

        $actions = $reader->getActions(
          new ReflectionClass(ActionButtonTestCase::class),
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
        $reader = new ActionButtonReader(new AnnotationReader());

        $e = null;
        try {
            $reader->getActions(
              new ReflectionClass(NoTemplateActionButtonTestCase::class),
              []
            );
        } catch (MissingAnnotationArgumentException $e) {}

        $this->assertNotNull($e);
        $this->assertEquals(
          sprintf(
            'Argument "%s" is mandatory for annotation %s on %s.',
            'template',
            ActionButton::class,
            NoTemplateActionButtonTestCase::class,
          ),
          $e->getMessage(),
        );
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
        $reader = new ActionButtonReader(new AnnotationReader());

        $actions = $reader->getActions($class, []);
        $this->assertCount(1, $actions);
        $this->assertEquals(['template' => 'test.html.twig'],
            current($actions));
    }
}

/**
 * @ActionButton(template="test.html.twig")
 */
class ActionButtonTestCase
{

}

/**
 * @ActionButton()
 */
class NoTemplateActionButtonTestCase
{

}