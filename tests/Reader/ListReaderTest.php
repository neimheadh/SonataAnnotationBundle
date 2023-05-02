<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Reader;

use Exception;
use InvalidArgumentException;
use Neimheadh\SonataAnnotationBundle\Admin\AnnotationAdmin;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ListAction;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ListAssociationField;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ListField;
use Neimheadh\SonataAnnotationBundle\AnnotationReader;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use Neimheadh\SonataAnnotationBundle\Reader\ListReader;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Extension\CreateNewAnnotationAdminTrait;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Entity\EmptyEntity;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\ArgumentAnnotation\ArgumentAnnotation;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\TestAdminAnnotationFields;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\TestAdminAnnotationFieldsAttribute;
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
     * @throws Exception
     */
    public function shouldBookListHaveAuthorAndTitles(): void
    {
        /** @var TestContainer $container */
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
     * @throws Exception
     */
    public function shouldBookListHaveImportAction(): void
    {
        $container = static::getContainer();
        /** @var AnnotationAdmin $admin */
        $admin = $container->get('app.admin.Book');

        $list = $admin->getList();
        $actions = $list->get('_actions');
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
        } catch (MissingAnnotationArgumentException $e) {
        }

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
        $admin = $this->createNewAnnotationAdmin($class->getName());

        $fields = $admin->getListFieldDescriptions();

        $this->assertEquals([
            'book.id',
            'id',
            '_actions',
        ], array_keys($fields));

        $this->assertEquals([
            'test' => [
                'template' => 'test.action.html.twig',
            ],
        ], $fields['_actions']->getOption('actions'));
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
        $reader = new ListReader(new AnnotationReader());
        $class = new ReflectionClass(TestAdminAnnotationFields::class);
        $mapper = $this->createNewListMapper();

        $reader->configureFields($class, $mapper);

        $this->assertEquals(['id', 'name', '_actions'], $mapper->keys());

        $class = new ReflectionClass(TestAdminAnnotationFieldsAttribute::class);

        $reader->configureFields(
            $class,
            $mapper = $this->createNewListMapper()
        );

        $this->assertEquals(['id', 'name', '_actions'], $mapper->keys());
    }

    /**
     * Test multiple list action works.
     *
     * @link https://github.com/neimheadh/SonataAnnotationBundle/issues/10
     * @test
     * @functionnal
     *
     * @return void
     */
    public function shouldAllowMultipleListActionAttribute(): void
    {
        $reader = new ListReader(new AnnotationReader());
        $class = new ReflectionClass(MultipleListAction::class);
        $mapper = $this->createNewListMapper();

        $reader->configureFields($class, $mapper);
        $actions = $mapper->get('_actions');

        $this->assertEquals([
            'show' => [
                'template' => '@SonataAdmin/CRUD/list__action_show.html.twig',
            ],
            'edit' => [
                'template' => '@SonataAdmin/CRUD/list__action_edit.html.twig',
            ],
            'delete' => [
                'template' => '@SonataAdmin/CRUD/list__action_delete.html.twig',
            ],
            'import' => [
                'template' => 'import_list_button.html.twig',
            ],
        ], $actions->getOption('actions'));
    }

    /**
     * Create new empty list mapper.
     *
     * @param string $class Entity class.
     *
     * @return ListMapper
     * @throws Exception
     */
    private function createNewListMapper(
        string $class = EmptyEntity::class
    ): ListMapper {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var ListBuilderInterface $listBuilder */
        $listBuilder = $container->get('sonata.admin.builder.orm_list');

        $mapper = new ListMapper(
            $listBuilder,
            new FieldDescriptionCollection(),
            $this->createNewAnnotationAdmin($class),
        );

        $mapper->getAdmin()->hasListFieldDescription('_actions')
        && $mapper->getAdmin()->removeListFieldDescription('_actions');

        return $mapper;
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


#[ListAction(name: "show")]
#[ListAction(name: "edit")]
#[ListAction(name: "delete")]
#[ListAction(name: "import", options: ['template' => 'import_list_button.html.twig'])]
class MultipleListAction
{

}