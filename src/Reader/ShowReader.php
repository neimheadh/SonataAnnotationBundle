<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use InvalidArgumentException;
use KunicMarko\SonataAnnotationBundle\Annotation\ShowAssociationField;
use KunicMarko\SonataAnnotationBundle\Annotation\ShowField;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use ReflectionClass;
use Sonata\AdminBundle\Show\ShowMapper;

use function array_key_exists;
use function array_merge;
use function ksort;

/**
 * Show configuration reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class ShowReader
{

    use AnnotationReaderTrait;

    /**
     * Build show fields configuration.
     *
     * @param ReflectionClass $class
     * @param ShowMapper      $showMapper
     *
     * @return void
     */
    public function configureFields(
        ReflectionClass $class,
        ShowMapper $showMapper
    ): void {
        $propertiesAndMethodsWithPosition = [];
        $propertiesAndMethodsWithoutPosition = [];

        //
        // Properties
        //

        foreach ($class->getProperties() as $property) {
            foreach ($this->getPropertyAnnotations($property) as $annotation) {
                if (!$annotation instanceof ShowField) {
                    continue;
                }

                // the name property changes for ShowAssociationField
                $name = $property->getName();
                if ($annotation instanceof ShowAssociationField) {
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
                        'settings' => $annotation->getSettings(),
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
                    'settings' => $annotation->getSettings(),
                ];
            }
        }

        //
        // Methods
        //

        foreach ($class->getMethods() as $method) {
            /** @var ShowField|null $annotation */
            if ($annotation = $this->getMethodAnnotation(
                $method,
                ShowField::class
            )) {
                $name = $method->getName();

                if (!isset($annotation->position)) {
                    $propertiesAndMethodsWithoutPosition[] = [
                        'name' => $name,
                        'settings' => $annotation->getSettings(),
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
                    'settings' => $annotation->getSettings(),
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
            $showMapper->add(
                $propertyAndMethod['name'],
                ...
                $propertyAndMethod['settings']
            );
        }
    }

}
