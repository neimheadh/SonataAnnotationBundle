<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Reader;

use Exception;
use InvalidArgumentException;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\DatagridValues;
use Neimheadh\SonataAnnotationBundle\AnnotationReader;
use Neimheadh\SonataAnnotationBundle\Reader\DatagridValuesReader;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\ArgumentAnnotation\ArgumentAnnotation;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * DatagridValuesReader test suite.
 */
class DatagridValuesReaderTest extends TestCase
{

    /**
     * Test DatagridValuesAnnotation support.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldSupportDatagridValuesAnnotation(): void
    {
        $reader = new DatagridValuesReader(new AnnotationReader());

        $values = $reader->getDatagridValues(
          new ReflectionClass(DatagridValuesReaderTestCase::class)
        );

        $this->assertCount(1, $values);
        $this->assertEquals(['_sort_by' => 'p.name'], $values);

        // Also test without annotation.
        $this->assertCount(0, $reader->getDatagridValues(
          new ReflectionClass(self::class),
        ));

        // Just test that the annotation cannot use a non-existing value.
        $annotation = new DatagridValues();
        $e = null;
        try {
            $annotation->nonExist = '';
        } catch (InvalidArgumentException $e) {}
        $this->assertNotNull($e);
        $this->assertEquals('Unknown property "nonExist".', $e->getMessage());

        $e = null;
        try {
            $tst = $annotation->nonExist;
        } catch (InvalidArgumentException $e) {}
        $this->assertNotNull($e);
        $this->assertEquals('Unknown property "nonExist".', $e->getMessage());
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
        $reader = new DatagridValuesReader(new AnnotationReader());

        $values = $reader->getDatagridValues($class);
        $this->assertCount(1, $values);
        $this->assertEquals(['_sort_by' => 'p.name'], $values);
    }
}

/**
 * @DatagridValues({"_sort_by": "p.name"})
 */
class DatagridValuesReaderTestCase
{

}