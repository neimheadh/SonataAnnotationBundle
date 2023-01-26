<?php

namespace KunicMarko\SonataAnnotationBundle\Tests\Reader;

use Doctrine\Common\Annotations\AnnotationReader;
use InvalidArgumentException;
use KunicMarko\SonataAnnotationBundle\Admin\AnnotationAdmin;
use KunicMarko\SonataAnnotationBundle\Annotation\DatagridAssociationField;
use KunicMarko\SonataAnnotationBundle\Annotation\DatagridField;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use KunicMarko\SonataAnnotationBundle\Reader\DatagridReader;
use KunicMarko\SonataAnnotationBundle\Tests\Resources\Extension\CreateNewAnnotationAdminTrait;
use ReflectionClass;
use Sonata\AdminBundle\Datagrid\Datagrid;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\DoctrineORMAdminBundle\Builder\DatagridBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;

/**
 * DatagridReader test suite.
 */
class DatagridReaderTest extends KernelTestCase
{

    use CreateNewAnnotationAdminTrait;

    /**
     * Test book datagrid fields.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldBookHaveCorrectDatagridFields(): void
    {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var AnnotationAdmin $admin */
        $admin = $container->get('app.admin.Book');

        /** @var Datagrid $datagrid */
        $datagrid = $admin->getDatagrid();
        $this->assertTrue($datagrid->hasFilter('id'));
        $this->assertTrue($datagrid->hasFilter('title'));
        $this->assertTrue($datagrid->hasFilter('author.name'));
    }

    /**
     * Test DatagridFieldAssociation name control.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldDatagridAssociationFieldHaveName(): void
    {
        $reader = new DatagridReader(new AnnotationReader());

        $e = null;
        try {
            $reader->configureFields(
              new ReflectionClass(DatagridAssociationWithoutName::class),
              $this->createNewDatagridMapper(),
            );
        } catch (MissingAnnotationArgumentException $e) {
        }
        $this->assertNotNull($e);
        $this->assertEquals(
          sprintf(
            'Argument "field" is mandatory for annotation %s.',
            DatagridAssociationField::class
          ),
          $e->getMessage(),
        );
    }

    /**
     * Datagrid field should not have duplicated position.
     *
     * @test
     * @functional
     *
     * @return void
     */
    public function shouldDatagridNotHaveDuplicatedPosition(): void
    {
        $reader = new DatagridReader(new AnnotationReader());

        $e = null;
        try {
            $reader->configureFields(
              new ReflectionClass(DatagridDuplicatedPosition::class),
              $this->createNewDatagridMapper(),
            );
        } catch (InvalidArgumentException $e) {
        }
        $this->assertNotNull($e);
        $this->assertEquals(
          sprintf(
            'Position "1" is already in use by "%s", try setting a different position for "%s".',
            'field1',
            'field2',
          ),
          $e->getMessage(),
        );
    }

    /**
     * Create new empty list mapper.
     *
     * @return DatagridMapper
     */
    private function createNewDatagridMapper(): DatagridMapper
    {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var DatagridBuilder $datagridBuilder */
        $datagridBuilder = $container->get('sonata.admin.builder.orm_datagrid');
        $admin = $this->createNewAnnotationAdmin();
        $datagrid = $datagridBuilder->getBaseDatagrid($admin);

        return new DatagridMapper(
          $datagridBuilder,
          $datagrid,
          $admin,
        );
    }

}

class DatagridAssociationWithoutName
{

    /**
     * @DatagridAssociationField()
     */
    public string $field = '';

}

class DatagridDuplicatedPosition
{

    /**
     * @DatagridField(position=1)
     */
    public string $field1;

    /**
     * @DatagridField(position=1)
     */
    public string $field2;

}