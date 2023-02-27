<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use KunicMarko\SonataAnnotationBundle\Annotation\ExportAssociationField;
use KunicMarko\SonataAnnotationBundle\Annotation\ExportField;
use KunicMarko\SonataAnnotationBundle\Annotation\ExportFormats;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Export configuration reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class ExportReader extends AbstractReader
{

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
                if ($annotation instanceof ExportField) {
                    $this->stackExportProperty($property, $annotation, $fields);
                }
            }
        }

        foreach ($class->getMethods() as $method) {
            if ($annotation = $this->getMethodAnnotation(
                $method,
                ExportField::class
            )) {
                $this->stackExportProperty($method, $annotation, $fields);
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

    /**
     * Stack property or method to exported fields.
     *
     * @param ReflectionProperty|ReflectionMethod $property    Stacked property.
     * @param object                              $annotation  Annotation.
     * @param array                               $fields      Fields stack.
     *
     * @return void
     */
    private function stackExportProperty(
        $property,
        object $annotation,
        array &$fields
    ): void {
        if ($annotation instanceof ExportAssociationField) {
            $fieldName = $property->getName()
                . '.'
                . $annotation->field;

            $fields[$annotation->label ?? $fieldName] = $fieldName;

            return;
        }

        $label = $annotation->label ?? $property->getName();
        $fields[$label] = $property->getName();
    }

}
