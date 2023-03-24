<?php

namespace Neimheadh\SonataAnnotationBundle\Reader;

use Doctrine\Common\Annotations\Reader;
use Neimheadh\SonataAnnotationBundle\Annotation\AnnotationInterface;
use Neimheadh\SonataAnnotationBundle\Annotation\AssociationFieldInterface;
use Neimheadh\SonataAnnotationBundle\Annotation\RouteAnnotationInterface;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Annotation reader.
 */
abstract class AbstractReader
{

    /**
     * Doctrine annotation reader.
     *
     * @var Reader
     */
    protected Reader $annotationReader;

    /**
     * @param Reader $annotationReader Doctrine annotation reader.
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * Check the given annotation is well-defined.
     *
     * @param AnnotationInterface $annotation The annotation.
     * @param ReflectionClass     $class      Class having the annotation.
     *
     * @return void
     */
    protected function checkAnnotationIntegrity(
        AnnotationInterface $annotation,
        ReflectionClass $class
    ): void {
        if ($annotation instanceof AssociationFieldInterface
            && !isset($annotation->field)) {
            throw new MissingAnnotationArgumentException(
                $annotation,
                'field',
            );
        }

        if ($annotation instanceof RouteAnnotationInterface
            && !isset($annotation->name)) {
            throw new MissingAnnotationArgumentException(
                $annotation,
                'name',
                $class
            );
        }
    }

    /**
     * Get the field name for the given annotation.
     *
     * @param string $baseName   Annotation base name.
     * @param object $annotation Annotation object.
     *
     * @return string
     */
    protected function getAnnotationFieldName(
        string $baseName,
        object $annotation
    ): string {
        if ($annotation instanceof AssociationFieldInterface) {
            return sprintf('%s.%s', $baseName, $annotation->field);
        }

        return $baseName;
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
     * @param ReflectionClass $class              Entity class.
     * @param ?string         $annotationClass    If given, filter annotation
     *                                            having the specified class.
     *
     * @return array<object|AnnotationInterface>
     */
    protected function getClassAnnotations(
        ReflectionClass $class,
        ?string $annotationClass = null
    ): array {
        $annotations = $annotationClass === null
            ? $this->annotationReader->getClassAnnotations($class)
            : array_filter(
                $this->annotationReader->getClassAnnotations($class),
                fn(
                    object $annotation
                ) => $annotation instanceof $annotationClass
            );

        array_walk(
            $annotations,
            function (object $annotation) use ($class) {
                $annotation instanceof AnnotationInterface
                && $this->checkAnnotationIntegrity(
                    $annotation,
                    $class
                );
            }
        );

        return $annotations;
    }

    /**
     * Get class methods annotations.
     *
     * @param ReflectionClass $class           Entity class.
     * @param ?string         $annotationClass Annotation class.
     *
     * @return array<string, object|AnnotationInterface[]>
     */
    protected function getClassMethodsAnnotations(
        ReflectionClass $class,
        ?string $annotationClass = null
    ): array {
        $annotations = [];

        if ($annotationClass !== null) {
            foreach ($class->getMethods() as $method) {
                $annotations[$method->getName()] = $this->getMethodAnnotations(
                    $method,
                    $annotationClass
                );
            }
        }

        return $annotations;
    }

    /**
     * Get class properties annotations.
     *
     * @param ReflectionClass $class           Entity class.
     * @param ?string         $annotationClass Annotation class.
     *
     * @return array<string, object|AnnotationInterface[]>
     */
    protected function getClassPropertiesAnnotations(
        ReflectionClass $class,
        ?string $annotationClass = null
    ): array {
        $annotations = [];

        foreach ($class->getProperties() as $property) {
            $annotations[$property->getName()] = $this->getPropertyAnnotations(
                $property,
                $annotationClass
            );
        }

        return $annotations;
    }

    /**
     * Get the list of annotations for a given method.
     *
     * @param ReflectionMethod $method          Method.
     * @param string|null      $annotationClass Filter annotation having the
     *                                          specified class.
     *
     * @return array<object|AnnotationInterface>
     */
    protected function getMethodAnnotations(
        ReflectionMethod $method,
        string $annotationClass = null
    ): array {
        $annotations = $annotationClass === null
            ? $this->annotationReader->getMethodAnnotations($method)
            : array_filter(
                $this->annotationReader->getMethodAnnotations($method),
                fn(
                    object $annotation
                ) => $annotation instanceof $annotationClass
            );

        array_walk(
            $annotations,
            function (object $annotation) use ($method) {
                $annotation instanceof AnnotationInterface
                && $this->checkAnnotationIntegrity(
                    $annotation,
                    $method->getDeclaringClass()
                );
            }
        );

        return $annotations;
    }

    /**
     * Get the list of annotations for a given property.
     *
     * @param ReflectionProperty $property        Property.
     * @param ?string            $annotationClass If given, filter annotation
     *                                            having the specified class.
     *
     * @return array<object|AnnotationInterface>
     */
    protected function getPropertyAnnotations(
        ReflectionProperty $property,
        ?string $annotationClass = null
    ): array {
        $annotations = $annotationClass === null
            ? $this->annotationReader->getPropertyAnnotations($property)
            : array_filter(
                $this->annotationReader->getPropertyAnnotations($property),
                fn(
                    object $annotation
                ) => $annotation instanceof $annotationClass
            );

        array_walk(
            $annotations,
            function (object $annotation) use ($property) {
                $annotation instanceof AnnotationInterface
                && $this->checkAnnotationIntegrity(
                    $annotation,
                    $property->getDeclaringClass()
                );
            }
        );

        return $annotations;
    }

}