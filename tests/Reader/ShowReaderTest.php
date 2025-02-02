<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Reader;

use Exception;
use InvalidArgumentException;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ShowAssociationField;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ShowField;
use Neimheadh\SonataAnnotationBundle\AnnotationReader;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use Neimheadh\SonataAnnotationBundle\Reader\FormReader;
use Neimheadh\SonataAnnotationBundle\Reader\ShowReader;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Extension\CreateNewAnnotationAdminTrait;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Entity\Book\Author;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\ArgumentAnnotation\ArgumentAnnotation;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\TestAdminAnnotationFields;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\TestAdminAnnotationFieldsAttribute;
use ReflectionClass;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Builder\ShowBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;

/**
 * ShowReader test suite.
 */
class ShowReaderTest extends KernelTestCase
{

    use CreateNewAnnotationAdminTrait;

    /**
     * Test show annotations are well-supported.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldSupportAnnotations(): void
    {
        $reader = new ShowReader(new AnnotationReader());
        $showMapper = $this->createNewShowMapper();

        $reader->configureFields(
            new ReflectionClass(ShowReaderTestCase::class),
            $showMapper,
        );

        $this->assertTrue($showMapper->has('name'));
        $this->assertEquals(
            [
                'sex',
                'getString',
                'name',
                'author.genre',
                'getStringEnd',
            ],
            $showMapper->keys()
        );
    }

    /**
     * Test that the ShowAssociationField field attribute is mandatory.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldAssociationAnnotationFieldMandatory(): void
    {
        $reader = new ShowReader(new AnnotationReader());

        $e = null;
        try {
            $reader->configureFields(
                new ReflectionClass(ShowReaderTestInvalidCase1::class),
                $this->createNewShowMapper(),
            );
        } catch (MissingAnnotationArgumentException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
            sprintf(
                'Argument "field" is mandatory for annotation %s.',
                ShowAssociationField::class
            ),
            $e->getMessage(),
        );
    }

    /**
     * Test class cannot have duplicated position.
     *
     * @test
     * @function
     *
     * @return void
     * @throws Exception
     */
    public function shouldNotHaveDuplicatePosition(): void
    {
        $reader = new ShowReader(new AnnotationReader());

        $e = null;
        try {
            $reader->configureFields(
                new ReflectionClass(ShowReaderTestInvalidCase2::class),
                $this->createNewShowMapper()
            );
        } catch (InvalidArgumentException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
            'Position "1" is already in use by "name", try setting a different position for "email".',
            $e->getMessage(),
        );

        $reader = new ShowReader(new AnnotationReader());

        $e = null;
        try {
            $reader->configureFields(
                new ReflectionClass(ShowReaderTestInvalidCase3::class),
                $this->createNewShowMapper()
            );
        } catch (InvalidArgumentException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
            'Position "1" is already in use by "name", try setting a different position for "getEmail".',
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
        $reader = new ShowReader(new AnnotationReader());

        $reader->configureFields(
            $class,
            $form = $this->createNewShowMapper()
        );

        $this->assertEquals([
            'book.id',
            'id',
        ], $form->keys());
    }

    /**
     * Test admin annotation fields system.
     *
     * @test
     * @functionnal
     *
     * @return void
     * @throws Exception
     */
    public function shouldAdminAnnotationWorks(): void
    {
        $reader = new ShowReader(new AnnotationReader());
        $class = new ReflectionClass(TestAdminAnnotationFields::class);

        $reader->configureFields(
            $class,
            $mapper = $this->createNewShowMapper()
        );

        $this->assertEquals(['id', 'name'], $mapper->keys());

        $class = new ReflectionClass(TestAdminAnnotationFieldsAttribute::class);

        $reader->configureFields(
            $class,
            $mapper = $this->createNewShowMapper()
        );

        $this->assertEquals(['id', 'name'], $mapper->keys());
    }

    /**
     * Create a new show mapper.
     *
     * @return ShowMapper
     * @throws Exception
     */
    private function createNewShowMapper(): ShowMapper
    {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var ShowBuilder $showBuilder */
        $showBuilder = $container->get('sonata.admin.builder.orm_show');

        return new ShowMapper(
            $showBuilder,
            new FieldDescriptionCollection(),
            $this->createNewAnnotationAdmin(),
        );
    }
}

class ShowReaderTestCase
{

    /**
     * @ShowField()
     *
     * @var string
     */
    private string $name = '';

    /**
     * @ShowAssociationField(field="genre")
     *
     * @var Author|null
     */
    private ?Author $author = null;

    /**
     * @ShowField(position=1)
     *
     * @var string|null
     */
    private ?string $sex = null;

    /**
     * @ShowField(position=2)
     *
     * @return string
     */
    public function getString(): string
    {
        return '';
    }

    /**
     * @ShowField()
     *
     * @return string
     */
    public function getStringEnd(): string
    {
        return '';
    }

}

class ShowReaderTestInvalidCase1
{

    /**
     * @ShowAssociationField()
     *
     * @var Author|null
     */
    private ?Author $author = null;

}

class ShowReaderTestInvalidCase2
{

    /**
     * @ShowField(position=1)
     *
     * @var string
     */
    private string $name = '';

    /**
     * @ShowField(position=1)
     * @var string
     */
    private string $email = '';

}

class ShowReaderTestInvalidCase3
{

    /**
     * @ShowField(position=1)
     *
     * @var string
     */
    private string $name = '';

    /**
     * @ShowField(position=1)
     *
     * @return  string
     */
    public function getEmail(): string
    {
        return '';
    }

}