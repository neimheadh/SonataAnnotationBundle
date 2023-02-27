<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Reader;

use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use Neimheadh\SonataAnnotationBundle\Annotation\ActionButton;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use Neimheadh\SonataAnnotationBundle\Reader\ActionButtonReader;
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