<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use InvalidArgumentException;
use KunicMarko\SonataAnnotationBundle\Annotation\ListAction;
use KunicMarko\SonataAnnotationBundle\Annotation\ListAssociationField;
use KunicMarko\SonataAnnotationBundle\Annotation\ListField;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use ReflectionClass;
use Sonata\AdminBundle\Datagrid\ListMapper;

use function array_key_exists;
use function array_merge;
use function ksort;

/**
 * List configuration reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class ListReader
{

    use AnnotationReaderTrait;

    /**
     * Build list field configurations.
     *
     * @param ReflectionClass $class      Entity class.
     * @param ListMapper      $listMapper Admin list mapper.
     *
     * @return void
     */
    public function configureFields(
        ReflectionClass $class,
        ListMapper $listMapper
    ): void {
        $propertiesAndMethodsWithPosition = [];
        $propertiesAndMethodsWithoutPosition = [];

        //
        // Properties
        //

        foreach ($class->getProperties() as $property) {
            foreach ($this->getPropertyAnnotations($property) as $annotation) {
                if (!$annotation instanceof ListField) {
                    continue;
                }

                // the name property changes for ListAssociationField
                $name = $property->getName();
                if ($annotation instanceof ListAssociationField) {
                    if (!isset($annotation->field)) {
                        throw new MissingAnnotationArgumentException(
                            $annotation,
                            'field',
                        );
                    }

                    $name .= '.' . $annotation->field;
                }

                if (!isset($annotation->position)) {
                    $propertiesAndMethodsWithoutPosition[] = [
                        'name' => $name,
                        'annotation' => $annotation,
                    ];

                    continue;
                }

                if (array_key_exists(
                    $annotation->position,
                    $propertiesAndMethodsWithPosition
                )) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Position "%s" is already in use by "%s", try setting a different position for "%s".',
                            $annotation->position,
                            $propertiesAndMethodsWithPosition[$annotation->position]['name'],
                            $property->getName()
                        )
                    );
                }

                $propertiesAndMethodsWithPosition[$annotation->position] = [
                    'name' => $name,
                    'annotation' => $annotation,
                ];
            }
        }

        //
        // Methods
        //

        foreach ($class->getMethods() as $method) {
            /** @var ListField|null $annotation */
            if ($annotation = $this->getMethodAnnotation(
                $method,
                ListField::class
            )) {
                $name = $method->getName();

                if (!isset($annotation->position)) {
                    $propertiesAndMethodsWithoutPosition[] = [
                        'name' => $name,
                        'annotation' => $annotation,
                    ];

                    continue;
                }

                if (array_key_exists(
                    $annotation->position,
                    $propertiesAndMethodsWithPosition
                )) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Position "%s" is already in use by "%s", try setting a different position for "%s".',
                            $annotation->position,
                            $propertiesAndMethodsWithPosition[$annotation->position]['name'],
                            $name
                        )
                    );
                }

                $propertiesAndMethodsWithPosition[$annotation->position] = [
                    'name' => $name,
                    'annotation' => $annotation,
                ];
            }
        }

        //
        // Sorting
        //

        ksort($propertiesAndMethodsWithPosition);

        $propertiesAndMethods = array_merge(
            $propertiesAndMethodsWithPosition,
            $propertiesAndMethodsWithoutPosition
        );

        foreach ($propertiesAndMethods as $propertyAndMethod) {
            $this->addField(
                $propertyAndMethod['name'],
                $propertyAndMethod['annotation'],
                $listMapper
            );
        }

        //
        // Actions
        //

        if ($actions = $this->getListActions($class)) {
            $listMapper->add('_action', null, [
                'actions' => $actions,
            ]);
        }
    }

    /**
     * Add field to list.
     *
     * @param string     $name       Field name.
     * @param ListField  $annotation Annotation.
     * @param ListMapper $listMapper Admin list mapper.
     *
     * @return void
     */
    private function addField(
        string $name,
        ListField $annotation,
        ListMapper $listMapper
    ): void {
        $listMapper->add($name, ...$annotation->getSettings());
    }

    /**
     * Get list of actions.
     *
     * @param ReflectionClass $class Entity class.
     *
     * @return array
     */
    private function getListActions(ReflectionClass $class): array
    {
        $actions = [];
        /** @var array<object|ListAction> $annotations */
        $annotations = $this->annotationReader->getClassAnnotations($class);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof ListAction) {
                if (!isset($annotation->name)) {
                    throw new MissingAnnotationArgumentException(
                        $annotation,
                        'name',
                        $class
                    );
                }

                $actions[$annotation->name] = $annotation->options;
            }
        }

        return $actions;
    }

}
