<?php

namespace KunicMarko\SonataAnnotationBundle\Exception;

use InvalidArgumentException;
use KunicMarko\SonataAnnotationBundle\Annotation\AnnotationInterface;
use ReflectionClass;

/**
 * A mandatory argument is missing on an annotation.
 */
class MissingAnnotationArgumentException extends InvalidArgumentException
{

    /**
     * @param AnnotationInterface|string  $annotation   Annotation object or
     *                                                  class.
     * @param string                      $argumentName Argument name.
     * @param ReflectionClass|string|null $class        Class using the
     *                                                  annotation.
     */
    public function __construct(
        $annotation,
        string $argumentName,
        $class = null
    ) {
        if ($annotation instanceof AnnotationInterface) {
            $annotation = get_class($annotation);
        }

        if ($class instanceof ReflectionClass) {
            $class = $class->getName();
        }

        if (!is_string($annotation)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid annotation (string|%s) attribute.',
                    AnnotationInterface::class,
                )
            );
        }

        if ($class !== null && !is_string($class)) {
            throw new InvalidArgumentException(
                'Invalid class (string|ReflectionClass|null) attribute.',
            );
        }

        if ($class) {
            $message = sprintf(
                'Argument "%s" is mandatory for annotation %s on %s.',
                $argumentName,
                $annotation,
                $class
            );
        } else {
            $message = sprintf(
                'Argument "%s" is mandatory for annotation %s.',
                $argumentName,
                $annotation,
            );
        }

        parent::__construct($message);
    }

}