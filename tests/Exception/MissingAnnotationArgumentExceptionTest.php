<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Exception;

use InvalidArgumentException;
use KunicMarko\SonataAnnotationBundle\Annotation\Access;
use KunicMarko\SonataAnnotationBundle\Annotation\AnnotationInterface;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * MissingAnnotationArgumentExceptionTest class test suite.
 */
class MissingAnnotationArgumentExceptionTest extends TestCase
{

    /**
     * Test that exception should have correct arguments.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldHaveCorrectArguments(): void
    {
        $e = null;
        try {
            new MissingAnnotationArgumentException(
              new stdClass(),
              'argument',
            );
        } catch (InvalidArgumentException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
          sprintf(
            'Invalid annotation (string|%s) attribute.',
            AnnotationInterface::class
          ),
          $e->getMessage(),
        );

        $e = null;
        try {
            new MissingAnnotationArgumentException(
              new Access(),
              'argument',
              new stdClass(),
            );
        } catch (InvalidArgumentException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
          'Invalid class (string|ReflectionClass|null) attribute.',
          $e->getMessage(),
        );
    }

}