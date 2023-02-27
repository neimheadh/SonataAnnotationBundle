<?php

namespace Neimheadh\SonataAnnotationBundle\Reader;

use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use Neimheadh\SonataAnnotationBundle\Annotation\ActionAnnotationInterface;
use Neimheadh\SonataAnnotationBundle\Annotation\PositionAnnotationInterface;
use ReflectionClass;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Mapper\MapperInterface;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Field configuration reader.
 */
class AbstractFieldConfigurationReader extends AbstractReader
{

    /**
     * Associated annotation class.
     *
     * @var string|null
     */
    protected ?string $annotationClass;

    /**
     * @param Reader  $annotationReader Doctrine annotation reader.
     * @param ?string $annotationClass  Associated annotation class.
     */
    public function __construct(
        Reader $annotationReader,
        ?string $annotationClass = null
    ) {
        parent::__construct($annotationReader);
        $this->annotationClass = $annotationClass;
    }

    /**
     * Build list field configurations.
     *
     * @param ReflectionClass $class  Entity class.
     * @param MapperInterface $mapper Admin mapper.
     *
     * @return void
     */
    public function configureFields(
        ReflectionClass $class,
        MapperInterface $mapper
    ): void {
        $this->configureReaderFields(
            $class,
            $mapper,
            $this->annotationClass
        );
    }

    /**
     * Configure fields with positions.
     *
     * @param ReflectionClass $class           Read entity class.
     * @param MapperInterface $mapper          Admin mapper.
     * @param string|null     $annotationClass Filtered annotation class.
     * @param string|null     $action          Current action.
     *
     * @return void
     */
    protected function configureReaderFields(
        ReflectionClass $class,
        MapperInterface $mapper,
        ?string $annotationClass = null,
        ?string $action = null
    ): void {
        $fields = $this->loadPositionAnnotationFields(
            $class,
            $annotationClass,
            $action
        );
        array_walk(
            $fields,
            fn(array $field) => $this->addMapperProperty($mapper, $field)
        );
    }

    /**
     * Check if given annotation is supported.
     *
     * @param object      $annotation Annotation.
     * @param string|null $action     Current action.
     *
     * @return bool
     */
    protected function isSupported(object $annotation, ?string $action): bool
    {
        return !$annotation instanceof ActionAnnotationInterface
            || $action === null
            || !isset($annotation->action)
            || $annotation->action === $action;
    }

    /**
     * Add property to datagrid mapper.
     *
     * @param DatagridMapper $mapper   Datagrid mapper.
     * @param string         $name     Property name.
     * @param array          $settings Annotation settings.
     *
     * @return void
     */
    private function addDatagridProperty(
        DatagridMapper $mapper,
        string $name,
        array $settings
    ): void {
        $mapper->add(
            $name,
            $settings[0] ?? null,
            $settings[1] ?? [],
            $settings[2] ?? [],
        );
    }

    /**
     * Add property to form mapper.
     *
     * @param FormMapper $mapper   Datagrid mapper.
     * @param string     $name     Property name.
     * @param array      $settings Annotation settings.
     *
     * @return void
     */
    private function addFormProperty(
        FormMapper $mapper,
        string $name,
        array $settings
    ): void {
        $mapper->add(
            $name,
            $settings[0] ?? null,
            $settings[1] ?? [],
            $settings[2] ?? [],
        );
    }

    /**
     * Add property to list mapper.
     *
     * @param ListMapper $mapper   List mapper.
     * @param string     $name     Property name.
     * @param array      $settings Annotation settings.
     *
     * @return void
     */
    private function addListProperty(
        ListMapper $mapper,
        string $name,
        array $settings
    ): void {
        $mapper->add(
            $name,
            $settings[0] ?? null,
            $settings[1] ?? [],
        );
    }

    /**
     * Add a property to the given mapper.
     *
     * @param MapperInterface $mapper   Admin mapper.
     * @param array           $property Property information.
     *
     * @return void
     */
    private function addMapperProperty(
        MapperInterface $mapper,
        array $property
    ): void {
        $settings = $property['annotation']->getSettings();

        if ($mapper instanceof DatagridMapper) {
            $this->addDatagridProperty($mapper, $property['name'], $settings);
        } elseif ($mapper instanceof FormMapper) {
            $this->addFormProperty($mapper, $property['name'], $settings);
        } elseif ($mapper instanceof ListMapper) {
            $this->addListProperty($mapper, $property['name'], $settings);
        } elseif ($mapper instanceof ShowMapper) {
            $this->addShowProperty($mapper, $property['name'], $settings);
        }
    }

    /**
     * Add property to show mapper.
     *
     * @param ShowMapper $mapper   Show mapper.
     * @param string     $name     Property name.
     * @param array      $settings Annotation settings.
     *
     * @return void
     */
    private function addShowProperty(
        ShowMapper $mapper,
        string $name,
        array $settings
    ): void {
        $mapper->add(
            $name,
            $settings[0] ?? null,
            $settings[1] ?? [],
        );
    }

    /**
     * Load class properties with position annotation.
     *
     * @param ReflectionClass $class           Entity class.
     * @param string|null     $annotationClass Reader annotation class.
     * @param string|null     $action          Current action.
     *
     * @return array
     */
    private function loadPositionAnnotationFields(
        ReflectionClass $class,
        ?string $annotationClass,
        ?string $action
    ): array {
        $propertiesAndMethods = array_merge(
            $this->getClassPropertiesAnnotations($class, $annotationClass),
            $this->getClassMethodsAnnotations($class, $annotationClass)
        );

        $propertiesWithPosition = [];
        $propertiesWithoutPosition = [];

        foreach ($propertiesAndMethods as $name => $annotations) {
            /** @var PositionAnnotationInterface $annotation */
            foreach ($annotations as $annotation) {
                if (!$this->isSupported($annotation, $action)) {
                    continue;
                }

                $name = $this->getAnnotationFieldName($name, $annotation);

                $this->stackProperty(
                    $name,
                    $annotation,
                    $propertiesWithPosition,
                    $propertiesWithoutPosition
                );
            }
        }

        ksort($propertiesWithPosition);

        return array_merge(
            $propertiesWithPosition,
            $propertiesWithoutPosition
        );
    }

    /**
     * Stack given annotation as a property with or without position.
     *
     * @param string $name                      Property name.
     * @param object $annotation                Annotation.
     * @param array  $propertiesWithPosition    Properties with position stack.
     * @param array  $propertiesWithoutPosition Properties without position
     *                                          stack.
     *
     * @return void
     */
    protected function stackProperty(
        string $name,
        object $annotation,
        array &$propertiesWithPosition,
        array &$propertiesWithoutPosition
    ): void {
        if (!isset($annotation->position)) {
            $propertiesWithoutPosition[] = [
                'name' => $name,
                'annotation' => $annotation,
            ];

            return;
        }

        if (array_key_exists(
            $annotation->position,
            $propertiesWithPosition
        )) {
            throw new InvalidArgumentException(
                sprintf(
                    'Position "%s" is already in use by "%s", try setting a different position for "%s".',
                    $annotation->position,
                    $propertiesWithPosition[$annotation->position]['name'],
                    $name
                )
            );
        }

        $propertiesWithPosition[$annotation->position] = [
            'name' => $name,
            'annotation' => $annotation,
        ];
    }

}