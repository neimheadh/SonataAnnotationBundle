<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Reader;

use Doctrine\Common\Annotations\AnnotationReader;
use KunicMarko\SonataAnnotationBundle\Annotation\ExportAssociationField;
use KunicMarko\SonataAnnotationBundle\Annotation\ExportField;
use KunicMarko\SonataAnnotationBundle\Annotation\ExportFormats;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use KunicMarko\SonataAnnotationBundle\Reader\ExportReader;
use KunicMarko\SonataAnnotationBundle\Tests\Resources\Model\Author;
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

}

/**
 * @ExportFormats({"json"})
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