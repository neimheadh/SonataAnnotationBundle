<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use Doctrine\Common\Annotations\Reader;
use KunicMarko\SonataAnnotationBundle\Annotation\AnnotationInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Annotation reader trait.
 *
 * @internal
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
trait AnnotationReaderTrait
{

    /**
     * Doctrine annotation reader.
     *
     * @var Reader
     */
    private Reader $annotationReader;

    /**
     * @param Reader $annotationReader Doctrine annotation reader.
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * Get a specific annotation for the given entity class.
     *
     * @param ReflectionClass $class      Entity class.
     * @param string          $annotation Annotation class.
     *
     * @return object|AnnotationInterface|null
     */
    protected function getClassAnnotation(
        ReflectionClass $class,
        string $annotation
    ): ?object {
        return $this->annotationReader->getClassAnnotation($class, $annotation);
    }

    /**
     * Get the list of annotations for a given entity class.
     *
     * @param ReflectionClass $class Entity class.
     *
     * @return array<object|AnnotationInterface>
     */
    protected function getClassAnnotations(ReflectionClass $class): array
    {
        return $this->annotationReader->getClassAnnotations($class);
    }

    /**
     * Get the list of annotations for a given property.
     *
     * @param ReflectionProperty $property Property.
     *
     * @return array<object|AnnotationInterface>
     */
    protected function getPropertyAnnotations(
        ReflectionProperty $property
    ): array {
        return $this->annotationReader->getPropertyAnnotations($property);
    }

    /**
     * Get a method annotation.
     *
     * @param ReflectionMethod $method     Method.
     * @param string           $annotation Annotation class.
     *
     * @return object|AnnotationInterface|null
     */
    protected function getMethodAnnotation(
        ReflectionMethod $method,
        string $annotation
    ): ?object {
        return $this->annotationReader->getMethodAnnotation(
            $method,
            $annotation
        );
    }

}
