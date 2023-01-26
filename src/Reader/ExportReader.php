<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use KunicMarko\SonataAnnotationBundle\Annotation\ExportAssociationField;
use KunicMarko\SonataAnnotationBundle\Annotation\ExportField;
use KunicMarko\SonataAnnotationBundle\Annotation\ExportFormats;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use ReflectionClass;

/**
 * Export configuration reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class ExportReader
{

    use AnnotationReaderTrait;

    /**
     * Get exported fields.
     *
     * @param ReflectionClass $class Entity class.
     *
     * @return array<string, string> Label => property/method name list.
     */
    public function getFields(ReflectionClass $class): array
    {
        $fields = [];

        foreach ($class->getProperties() as $property) {
            foreach ($this->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof ExportAssociationField) {
                    if (!isset($annotation->field)) {
                        throw new MissingAnnotationArgumentException(
                          $annotation,
                          'field',
                        );
                    }

                    $fieldName = $property->getName()
                      . '.'
                      . $annotation->field;

                    $fields[$annotation->label ?? $fieldName] = $fieldName;
                    continue;
                }

                if ($annotation instanceof ExportField) {
                    $label = $annotation->label ?? $property->getName();
                    $fields[$label] = $property->getName();
                }
            }
        }

        foreach ($class->getMethods() as $method) {
            if ($annotation = $this->getMethodAnnotation(
              $method,
              ExportField::class
            )) {
                $label = $annotation->label ?? $method->getName();
                $fields[$label] = $method->getName();
            }
        }

        return $fields;
    }

    /**
     * Get list of extract formats.
     *
     * @param ReflectionClass $class Entity class.
     *
     * @return array
     */
    public function getFormats(ReflectionClass $class): array
    {
        /** @var ExportFormats|null $annotation */
        if ($annotation = $this->getClassAnnotation(
          $class,
          ExportFormats::class
        )) {
            return $annotation->formats;
        }

        return [];
    }

}
