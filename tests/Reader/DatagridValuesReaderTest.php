<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Reader;

use Doctrine\Common\Annotations\AnnotationReader;
use KunicMarko\SonataAnnotationBundle\Annotation\DatagridValues;
use KunicMarko\SonataAnnotationBundle\Reader\DatagridValuesReader;
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
    }
}

/**
 * @DatagridValues(values={"_sort_by": "p.name"})
 */
class DatagridValuesReaderTestCase
{

}