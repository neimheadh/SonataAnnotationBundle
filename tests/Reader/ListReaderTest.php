<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Reader;

use Doctrine\Common\Annotations\AnnotationReader;
use InvalidArgumentException;
use KunicMarko\SonataAnnotationBundle\Admin\AnnotationAdmin;
use KunicMarko\SonataAnnotationBundle\Annotation\ListAction;
use KunicMarko\SonataAnnotationBundle\Annotation\ListAssociationField;
use KunicMarko\SonataAnnotationBundle\Annotation\ListField;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use KunicMarko\SonataAnnotationBundle\Reader\ListReader;
use KunicMarko\SonataAnnotationBundle\Tests\Resources\Extension\CreateNewAnnotationAdminTrait;
use ReflectionClass;
use Sonata\AdminBundle\Builder\ListBuilderInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;

/**
 * ListReader test suite.
 */
class ListReaderTest extends KernelTestCase
{

    use CreateNewAnnotationAdminTrait;

    /**
     * Test book admin list should have author, book title and cover title.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldBookListHaveAuthorAndTitles(): void
    {
        $container = static::getContainer();
        /** @var AnnotationAdmin $admin */
        $admin = $container->get('app.admin.Book');

        $list = $admin->getList();
        $this->assertTrue($list->has('author.name'), 'Missing author column.');
        $this->assertTrue($list->has('title'), 'Missing title column.');
        $this->assertTrue(
          $list->has('getCoverTitle'),
          'Missing cover title column.'
        );

        $this->assertTrue(
          $list->get('id')->isIdentifier(),
          'Id should be an identifier.'
        );
        $this->assertFalse(
          $list->get('author.name')->isIdentifier(),
          'Author should not be an identifier.'
        );
        $this->assertFalse(
          $list->get('title')->isIdentifier(),
          'Title should not be an identifier.'
        );
        $this->assertFalse(
          $list->get('getCoverTitle')->isIdentifier(),
          'Cover title should not be an identifier.'
        );
    }

    /**
     * Test book admin list should have import action.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldBookListHaveImportAction(): void
    {
        $container = static::getContainer();
        /** @var AnnotationAdmin $admin */
        $admin = $container->get('app.admin.Book');

        $list = $admin->getList();
        $actions = $list->get('_action');
        $this->assertArrayHasKey('import', $actions->getOption('actions'));
        $this->assertEquals(['template' => 'import_list_button.html.twig'],
                            $actions->getOption('actions')['import']);
    }

    /**
     * Test list association field has field set.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldAssociationHaveFieldAttribute(): void
    {
        $reader = new ListReader(new AnnotationReader());
        $listMapper = $this->createNewListMapper();

        $e = null;
        try {
            $reader->configureFields(
              new ReflectionClass(MissingListAssociationField::class),
              $listMapper,
            );
        } catch (MissingAnnotationArgumentException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
          sprintf(
            'Argument "%s" is mandatory for annotation %s.',
            'field',
            ListAssociationField::class
          ),
          $e->getMessage(),
        );
    }

    /**
     * Test configured list fields doesn't have duplicated positions.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldNotHaveDuplicatedPosition(): void
    {
        $reader = new ListReader(new AnnotationReader());
        $listMapper = $this->createNewListMapper();

        $e = null;
        try {
            $reader->configureFields(
              new ReflectionClass(DuplicatedListFieldPosition::class),
              $listMapper,
            );
        } catch (InvalidArgumentException $e) {
        }
        $this->assertNotNull($e);
        $this->assertEquals(
          'Position "1" is already in use by "field1", try setting a different position for "field2".',
          $e->getMessage()
        );

        $e = null;
        try {
            $reader->configureFields(
              new ReflectionClass(DuplicatedListMethodPosition::class),
              $listMapper,
            );
        } catch (InvalidArgumentException $e) {
        }
        $this->assertNotNull($e);
        $this->assertEquals(
          'Position "1" is already in use by "getField1", try setting a different position for "getField2".',
          $e->getMessage()
        );
    }

    /**
     * Test list action name control.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldListActionHaveName(): void
    {
        $reader = new ListReader(new AnnotationReader());
        $listMapper = $this->createNewListMapper();
        
        $e = null;
        try {
            $reader->configureFields(
              new ReflectionClass(BadListAction::class),
              $listMapper
            );
        } catch (MissingAnnotationArgumentException $e) {}

        $this->assertNotNull($e);
        $this->assertEquals(
          sprintf(
            'Argument "name" is mandatory for annotation %s on %s.',
            ListAction::class,
            BadListAction::class
          ),
          $e->getMessage()
        );
    }

    /**
     * Create new empty list mapper.
     *
     * @return ListMapper
     */
    private function createNewListMapper(): ListMapper
    {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var ListBuilderInterface $listBuilder */
        $listBuilder = $container->get('sonata.admin.builder.orm_list');

        return new ListMapper(
          $listBuilder,
          new FieldDescriptionCollection(),
          $this->createNewAnnotationAdmin(),
        );
    }

}

class MissingListAssociationField
{

    /**
     * @ListAssociationField
     */
    public string $test = '';

}

class DuplicatedListFieldPosition
{

    /**
     * @ListField(position=1)
     */
    public string $field1 = '';

    /**
     * @ListField(position=1)
     */
    public string $field2 = '';

}

class DuplicatedListMethodPosition
{

    /**
     * @ListField(position=1)
     */
    public function getField1(): string
    {
        return '';
    }

    /**
     * @ListField(position=1)
     */
    public function getField2(): string
    {
        return '';
    }

}

/**
 * @ListAction()
 */
class BadListAction
{

}