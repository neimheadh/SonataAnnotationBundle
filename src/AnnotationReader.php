<?php

namespace Neimheadh\SonataAnnotationBundle;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Annotation reading trait.
 */
class AnnotationReader implements Reader
{

    private Reader $annotationReader;

    /**
     *
     */
    public function __construct()
    {
        $this->annotationReader = new DoctrineAnnotationReader();
    }


    /**
     * {@inheritDoc}
     */
    public function getClassAnnotations(ReflectionClass $class): array
    {
        return array_merge(
            $this->getAttributeAnnotations($class),
            $this->annotationReader->getClassAnnotations($class)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getClassAnnotation(ReflectionClass $class, $annotationName)
    {
        return current(
            $this->getAttributeAnnotations($class, $annotationName)
        ) ?: $this->annotationReader->getClassAnnotation(
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
            $this->annotationReader->getMethodAnnotations($method)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getMethodAnnotation(
        ReflectionMethod $method,
        $annotationName
    ) {
        return current(
            $this->getAttributeAnnotations(
                $method,
                $annotationName
            )
        ) ?: $this->annotationReader->getMethodAnnotation(
            $method,
            $annotationName
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertyAnnotations(ReflectionProperty $property)
    {
        return array_merge(
            $this->getAttributeAnnotations($property),
            $this->annotationReader->getPropertyAnnotations($property)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getPropertyAnnotation(
        ReflectionProperty $property,
        $annotationName
    ) {
        return current(
            $this->getAttributeAnnotations($property, $annotationName)
        ) ?: $this->annotationReader->getPropertyAnnotation(
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