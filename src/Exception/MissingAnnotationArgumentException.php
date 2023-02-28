<?php

namespace Neimheadh\SonataAnnotationBundle\Exception;

use InvalidArgumentException;
use Neimheadh\SonataAnnotationBundle\Annotation\AnnotationInterface;
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
        if (is_object($annotation)) {
            $annotation = get_class($annotation);
        }

        $class = $this->getClassName($class);
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

    /**
     * Get given class name.
     *
     * @param string|object|null $class Class.
     *
     * @return string|null
     */
    private function getClassName($class): ?string
    {
        if ($class instanceof ReflectionClass) {
            $class = $class->getName();
        }

        if ($class !== null && !is_string($class)) {
            throw new InvalidArgumentException(
                'Invalid class (string|ReflectionClass|null) attribute.',
            );
        }

        return $class;
    }

}