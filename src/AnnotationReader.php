<?php

namespace Neimheadh\SonataAnnotationBundle;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Annotation reading trait.
 */
class AnnotationReader extends DoctrineAnnotationReader
{

    /**
     * {@inheritDoc}
     */
    public function getClassAnnotations(ReflectionClass $class): array
    {
        return array_merge(
            $this->getAttributeAnnotations($class),
            parent::getClassAnnotations($class)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getClassAnnotation(ReflectionClass $class, $annotationName)
    {
        return current(
            $this->getAttributeAnnotations($class, $annotationName)
        ) ?: parent::getClassAnnotation(
            $class,
            $annotationName
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getMethodAnnotations(ReflectionMethod $method): array
    {
        return array_merge(
            $this->getAttributeAnnotations($method),
            parent::getMethodAnnotations($method)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertyAnnotations(ReflectionProperty $property): array
    {
        return array_merge(
            $this->getAttributeAnnotations($property),
            parent::getPropertyAnnotations($property)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertyAnnotation(
        ReflectionProperty $property,
        $annotationName
    ): ?object {
        return current(
            $this->getAttributeAnnotations($property, $annotationName)
        ) ?: parent::getPropertyAnnotation(
            $property,
            $annotationName
        );
    }

    /**
     * Get attribute annotations.
     *
     * @param object  $reflection Reflection element.
     * @param ?string $annotation Annotation class.
     *
     * @return array
     */
    private function getAttributeAnnotations(
        object $reflection,
        ?string $annotation = null
    ): array {
        $annotations = [];

        if (method_exists($reflection, 'getAttributes')) {
            $arguments = $reflection->getAttributes($annotation);

            foreach ($arguments as $argument) {
                if (method_exists($argument, 'newInstance')) {
                    $annotations[] = $argument->newInstance();
                }
            }
        }

        return $annotations;
    }

}