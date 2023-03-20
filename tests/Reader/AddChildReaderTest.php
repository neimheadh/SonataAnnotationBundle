<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Reader;

use Neimheadh\SonataAnnotationBundle\AnnotationReader;
use Neimheadh\SonataAnnotationBundle\Annotation\AddChild;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use Neimheadh\SonataAnnotationBundle\Reader\AddChildReader;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Book;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * AddChildReader test suite.
 */
class AddChildReaderTest extends TestCase
{

    /**
     * Test AddChild annotation argument controls.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldHaveCorrectArguments(): void
    {
        $reader = new AddChildReader(new AnnotationReader());
        $msg = sprintf(
          'Argument "%%s" is mandatory for annotation %s on %%s.',
          AddChild::class,
        );

        $class = new ReflectionClass(TestInvalidClassArgument::class);
        $e = null;
        try {
            $reader->getChildren($class);
        } catch (MissingAnnotationArgumentException $e) {
        }
        $this->assertNotNull($e);
        $this->assertEquals(
          sprintf($msg, 'class', TestInvalidClassArgument::class),
          $e->getMessage(),
        );

        $class = new ReflectionClass(TestInvalidFieldArgument::class);
        $e = null;
        try {
            $reader->getChildren($class);
        } catch (MissingAnnotationArgumentException $e) {
        }
        $this->assertNotNull($e);
        $this->assertEquals(
          sprintf($msg, 'field', TestInvalidFieldArgument::class),
          $e->getMessage(),
        );
    }

}

/**
 * @AddChild()
 */
class TestInvalidClassArgument
{

}

/**
 * @AddChild(class=Book::class)
 */
class TestInvalidFieldArgument
{

}