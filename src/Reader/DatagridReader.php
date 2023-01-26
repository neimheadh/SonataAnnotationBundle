<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use InvalidArgumentException;
use KunicMarko\SonataAnnotationBundle\Annotation\DatagridAssociationField;
use KunicMarko\SonataAnnotationBundle\Annotation\DatagridField;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use ReflectionClass;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use function array_key_exists;
use function array_merge;
use function ksort;

/**
 * Datagrid configuration reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class DatagridReader
{

    use AnnotationReaderTrait;

    /**
     * Configure given model class datagrid fields.
     *
     * @param ReflectionClass $class          Entity class.
     * @param DatagridMapper  $datagridMapper Admin datagrid mapper.
     *
     * @return void
     */
    public function configureFields(
      ReflectionClass $class,
      DatagridMapper $datagridMapper
    ): void {
        $propertiesWithPosition = [];
        $propertiesWithoutPosition = [];

        foreach ($class->getProperties() as $property) {
            foreach ($this->getPropertyAnnotations($property) as $annotation) {
                if (!$annotation instanceof DatagridField) {
                    continue;
                }

                // the name property changes for DatagridAssociationField
                $name = $property->getName();
                if ($annotation instanceof DatagridAssociationField) {
                    if (!isset($annotation->field)) {
                        throw new MissingAnnotationArgumentException(
                          $annotation,
                          'field',
                        );
                    }

                    $name .= '.' . $annotation->field;
                }

                if (!isset($annotation->position)) {
                    $propertiesWithoutPosition[] = [
                      'name' => $name,
                      'annotation' => $annotation,
                    ];

                    continue;
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
                        $property->getName()
                      )
                    );
                }

                $propertiesWithPosition[$annotation->position] = [
                  'name' => $name,
                  'annotation' => $annotation,
                ];
            }
        }

        ksort($propertiesWithPosition);

        $properties = array_merge(
          $propertiesWithPosition,
          $propertiesWithoutPosition
        );

        foreach ($properties as $property) {
            $datagridMapper->add(
                 $property['name'],
              ...$property['annotation']->getSettings()
            );
        }
    }

}
