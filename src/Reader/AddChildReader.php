<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use InvalidArgumentException;
use KunicMarko\SonataAnnotationBundle\Annotation\AddChild;
use ReflectionClass;

/**
 * @author Marko Kunic <kunicmarko20@gmail.com>
 */
final class AddChildReader
{
    use AnnotationReaderTrait;

    public function getChildren(ReflectionClass $class): array
    {
        $children = [];

        foreach ($this->getClassAnnotations($class) as $annotation) {
            if (!$annotation instanceof AddChild) {
                continue;
            }

            if (!isset($annotation->class)) {
                throw new InvalidArgumentException(
                  sprintf(
                    'Argument "class" is mandatory for annotation AddChild on class "%s".',
                    $class->getName(),
                  )
                );
            }

            if (!isset($annotation->field)) {
                throw new InvalidArgumentException(
                  sprintf(
                    'Argument "field" is mandatory for annotation AddChild on class "%s".',
                    $class->getName(),
                  )
                );
            }

            $children[$annotation->class] = $annotation->field;
        }

        return $children;
    }
}
