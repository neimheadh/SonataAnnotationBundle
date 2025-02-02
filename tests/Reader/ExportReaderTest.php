<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Reader;

use Exception;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ExportAssociationField;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ExportField;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ExportFormats;
use Neimheadh\SonataAnnotationBundle\AnnotationReader;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use Neimheadh\SonataAnnotationBundle\Reader\ExportReader;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Entity\Book\Author;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\ArgumentAnnotation\ArgumentAnnotation;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\TestAdminAnnotationFields;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\TestAdminAnnotationFieldsAttribute;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * ExportReader test suite.
 */
class ExportReaderTest extends TestCase
{

    /**
     * Test ExportReader annotations are well-supported.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldSupportAnnotation(): void
    {
        $reader = new ExportReader(new AnnotationReader());
        $class = new ReflectionClass(ExportReaderTestCase::class);

        $fields = $reader->getFields($class);
        $formats = $reader->getFormats($class);

        $this->assertEquals(['json'], $formats);
        $this->assertEquals(
          [
            'name' => 'name',
            'Custom name' => 'tag',
            'Author' => 'author.name',
            'author.genre' => 'author.genre',
            'exportedMethod' => 'exportedMethod',
          ],
          $fields,
        );
    }

    /**
     * Test the reader doesn't get any format by default.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldDefaultNotHaveFormat(): void
    {
        $reader = new ExportReader(new AnnotationReader());
        $formats = $reader->getFormats(new ReflectionClass(self::class));

        $this->assertEmpty($formats);
    }

    /**
     * Test the ExportAssociationField should have a field set.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldAssociationAnnotationHaveField(): void
    {
        $reader = new ExportReader(new AnnotationReader());

        $e = null;
        try {
            $reader->getFields(
              new ReflectionClass(
                ExportReaderTestAssociationNoFieldSetCase::class
              )
            );
        } catch (MissingAnnotationArgumentException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
          sprintf(
            'Argument "field" is mandatory for annotation %s.',
            ExportAssociationField::class,
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
        $reader = new ExportReader(new AnnotationReader());

        $fields = $reader->getFields($class);
        $formats = $reader->getFormats($class);

        $this->assertEquals(['json'], $formats);
        $this->assertEquals(['Id', 'Book id'], array_keys($fields));
    }

    /**
     * Test Admin annotation exportFields property.
     *
     * @test
     * @functionnal
     *
     * @return void
     */
    public function shouldHandleAdminFields(): void
    {
        $reader = new ExportReader(new AnnotationReader());
        $class = new ReflectionClass(TestAdminAnnotationFields::class);

        $fields = $reader->getFields($class);

        $this->assertEquals(['id', 'name'], array_keys($fields));

        $class = new ReflectionClass(TestAdminAnnotationFieldsAttribute::class);

        $fields = $reader->getFields($class);

        $this->assertEquals(['id', 'name'], array_keys($fields));
    }
}

/**
 * @ExportFormats("json")
 */
class ExportReaderTestCase
{

    /**
     * @ExportField()
     *
     * @var string
     */
    private string $name = '';

    /**
     * @ExportField("Custom name")
     *
     * @var string
     */
    private string $tag = '';

    /**
     * @ExportAssociationField(field="name", label="Author")
     * @ExportAssociationField(field="genre")
     *
     *
     * @var Author|null
     */
    private ?Author $author = null;

    /**
     * @ExportField()
     *
     * @return string
     */
    public function exportedMethod(): string
    {
        return '';
    }

}

class ExportReaderTestAssociationNoFieldSetCase
{

    /**
     * @ExportAssociationField()
     *
     * @var Author|null
     */
    private ?Author $author = null;

}