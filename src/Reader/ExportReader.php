<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Reader;

use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\Admin;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ExportAssociationField;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ExportField;
use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\ExportFormats;
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
        /** @var Admin|null $admin */
        $admin = $this->getClassAnnotation($class, Admin::class);

        $fields = $admin ? $admin->getExportFields() : [];
        $allFields = [];
        $properties = array_merge(
            $class->getProperties(),
            $class->getMethods()
        );

        foreach ($properties as $property) {
            $annotations = $property instanceof ReflectionProperty
                ? $this->getPropertyAnnotations($property)
                : $this->getMethodAnnotations($property);

            $this->stackExportProperty(
                $property,
                new ExportField(),
                $allFields
            );

            foreach ($annotations as $annotation) {
                if ($annotation instanceof ExportField) {
                    $this->stackExportProperty($property, $annotation, $fields);
                }
            }
        }

        return empty($fields) ? $allFields : $fields;
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
     * @param ReflectionProperty|ReflectionMethod $property   Stacked property.
     * @param object                              $annotation Annotation.
     * @param array                               $fields     Fields stack.
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

            $fields[$annotation->label ?: $fieldName] = $fieldName;

            return;
        }

        $label = $annotation->label ?: $property->getName();
        $fields[$label] = $property->getName();
    }

}
