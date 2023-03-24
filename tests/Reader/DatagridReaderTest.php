<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Reader;

use Exception;
use InvalidArgumentException;
use Neimheadh\SonataAnnotationBundle\Admin\AnnotationAdmin;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\DatagridAssociationField;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\DatagridField;
use Neimheadh\SonataAnnotationBundle\AnnotationReader;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\Compiler\AutoRegisterCompilerPass;
use Neimheadh\SonataAnnotationBundle\DependencyInjection\SonataAnnotationExtension;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use Neimheadh\SonataAnnotationBundle\Reader\DatagridReader;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Extension\CreateNewAnnotationAdminTrait;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Entity\EmptyEntity;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\ArgumentAnnotation\ArgumentAnnotation;
use ReflectionClass;
use Sonata\AdminBundle\Datagrid\Datagrid;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\DoctrineORMAdminBundle\Builder\DatagridBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
        $reader = new DatagridReader(new AnnotationReader());

        $container = new ContainerBuilder();
        $container->setParameter(
            SonataAnnotationExtension::PARAM_ENTITY_NAMESPACE,
            [$class->getNamespaceName()]
        );
        $container->setParameter(
            SonataAnnotationExtension::PARAM_MENU_USE_NAMESPACE,
            true
        );

        foreach (
            [
                'sonata.admin.builder.orm_datagrid',
            ] as $service
        ) {
            $container->set($service, static::getContainer()->get($service));
        }


        $reader->configureFields(
            $class,
            $datagrid = $this->createNewDatagridMapper($container)
        );

        $this->assertEquals([
            'book.id',
            'id',
        ], $datagrid->keys());
    }

    /**
     * Create new empty list mapper.
     *
     * @param ContainerInterface|null $container Test container.
     *
     * @return DatagridMapper
     * @throws Exception
     */
    private function createNewDatagridMapper(
        ContainerInterface $container = null
    ): DatagridMapper {
        /** @var TestContainer $container */
        $container = $container ?: static::getContainer();
        /** @var DatagridBuilder $datagridBuilder */
        $datagridBuilder = $container->get('sonata.admin.builder.orm_datagrid');
        $admin = $this->createNewAnnotationAdmin(EmptyEntity::class);
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