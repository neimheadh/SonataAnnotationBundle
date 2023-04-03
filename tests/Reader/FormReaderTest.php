<?php

namespace Neimheadh\SonataAnnotationBundle\Tests\Reader;

use Exception;
use InvalidArgumentException;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\FormField;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ListField;
use Neimheadh\SonataAnnotationBundle\AnnotationReader;
use Neimheadh\SonataAnnotationBundle\Reader\FormReader;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Extension\CreateNewAnnotationAdminTrait;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\ArgumentAnnotation\ArgumentAnnotation;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\TestAdminAnnotationFields;
use Neimheadh\SonataAnnotationBundle\Tests\Resources\Model\Test\TestAdminAnnotationFieldsAttribute;
use ReflectionClass;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Builder\FormContractor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;

/**
 * FormReader test suite.
 */
class FormReaderTest extends KernelTestCase
{

    use CreateNewAnnotationAdminTrait;

    /**
     * Test the form annotations are supported.
     *
     * @test
     * @functional
     *
     * @return void
     * @throws Exception
     */
    public function shouldSupportAnnotations(): void
    {
        $reader = new FormReader(new AnnotationReader());
        $class = new ReflectionClass(FormReaderTestCase::class);

        $formMapper = $this->createNewFormMapper();
        $reader->configureCreateFields($class, $formMapper);
        $this->assertTrue($formMapper->has('name'));
        $this->assertTrue($formMapper->has('email'));
        $this->assertFalse($formMapper->has('id'));
        $this->assertFalse($formMapper->has('phone'));
        $this->assertInstanceOf(
            TextType::class,
            $formMapper->get('name')->getType()->getInnerType()
        );
        $this->assertEquals(['sex', 'name', 'email'], $formMapper->keys());

        $formMapper = $this->createNewFormMapper();
        $reader->configureEditFields($class, $formMapper);
        $this->assertTrue($formMapper->has('name'));
        $this->assertTrue($formMapper->has('phone'));
        $this->assertFalse($formMapper->has('id'));
        $this->assertFalse($formMapper->has('email'));
        $this->assertInstanceOf(
            TextType::class,
            $formMapper->get('name')->getType()->getInnerType()
        );
        $this->assertEquals(['sex', 'name', 'phone'], $formMapper->keys());
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
        $reader = new FormReader(new AnnotationReader());
        $formMapper = $this->createNewFormMapper();

        $e = null;
        try {
            $reader->configureEditFields(
                new ReflectionClass(FormReaderTestDuplicatePositionCase::class),
                $formMapper
            );
        } catch (InvalidArgumentException $e) {
        }

        $this->assertNotNull($e);
        $this->assertEquals(
            'Position "1" is already in use by "name", try setting a different position for "email".',
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
        $reader = new FormReader(new AnnotationReader());

        $reader->configureFields(
            $class,
            $form = $this->createNewFormMapper()
        );

        $this->assertEquals([
            'book',
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
        $formReader = new FormReader(new AnnotationReader());
        $class = new ReflectionClass(TestAdminAnnotationFields::class);

        $formReader->configureFields(
            $class,
            $form = $this->createNewFormMapper()
        );

        $this->assertEquals(['name'], $form->keys());

        $class = new ReflectionClass(TestAdminAnnotationFieldsAttribute::class);

        $formReader->configureCreateFields(
            $class,
            $form = $this->createNewFormMapper()
        );
        $this->assertEquals(['name', 'label'], $form->keys());

        $formReader->configureEditFields(
            $class,
            $form = $this->createNewFormMapper()
        );
        $this->assertEquals(['name', 'title'], $form->keys());
    }

    /**
     * Test admin field errors.
     *
     * @test
     * @functionnal
     *
     * @return void
     */
    public function shouldMakeErrorOnAdminFieldTypes(): void
    {
        $reader = new FormReader(new AnnotationReader());
        $class = new ReflectionClass(InvalidAdminFieldClass::class);

        $e = null;
        try {
            $reader->configureFields(
                $class,
                $form = $this->createNewFormMapper()
            );
        } catch (InvalidArgumentException $e) {}

        $this->assertNotNull($e);
        $this->assertEquals(
            sprintf(
                'Array of %s expected, array contains an %s element.',
                FormField::class,
                Sonata\DatagridField::class
            ),
            $e->getMessage()
        );
    }

    /**
     * Create a new form mapper.
     *
     * @return FormMapper
     * @throws Exception
     */
    private function createNewFormMapper(): FormMapper
    {
        /** @var TestContainer $container */
        $container = static::getContainer();
        /** @var FormContractor $formContractor */
        $formContractor = $container->get('sonata.admin.builder.orm_form');
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $container->get('event_dispatcher');
        /** @var FormFactory $factory */
        $factory = $container->get('form.factory');

        return new FormMapper(
            $formContractor,
            new FormBuilder(null, null, $dispatcher, $factory),
            $this->createNewAnnotationAdmin(),
        );
    }

}

class FormReaderTestCase
{

    /**
     * @ListField()
     *
     * @var int
     */
    private int $id = 0;

    /**
     * @FormField()
     *
     * @var string
     */
    private string $name = '';

    /**
     * @FormField(action=FormField::ACTION_EDIT)
     *
     * @var string
     */
    private string $phone = '';

    /**
     * @FormField(action=FormField::ACTION_CREATE)
     *
     * @var string
     */
    private string $email = '';

    /**
     * @FormField(position=1)
     *
     * @var string
     */
    private string $sex = 'm';

}

class FormReaderTestDuplicatePositionCase
{

    /**
     * @FormField(position=1)
     *
     * @var string
     */
    private string $name = '';

    /**
     * @FormField(position=1)
     *
     * @var string
     */
    private string $email = '';

}

#[Sonata\Admin(formFields: [new Sonata\DatagridField()])]
class InvalidAdminFieldClass
{
    public ?string $test = null;
}