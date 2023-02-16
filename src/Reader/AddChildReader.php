<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use InvalidArgumentException;
use KunicMarko\SonataAnnotationBundle\Annotation\AddChild;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use ReflectionClass;

/**
 * AddChild annotation reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class AddChildReader
{

    use AnnotationReaderTrait;

    /**
     * Get admin children.
     *
     * @param ReflectionClass $class Entity class.
     *
     * @return array<string, string> Children model class => field list.
     */
    public function getChildren(ReflectionClass $class): array
    {
        $children = [];

        foreach ($this->getClassAnnotations($class) as $annotation) {
            if (!$annotation instanceof AddChild) {
                continue;
            }

            if (!isset($annotation->class)) {
                throw new MissingAnnotationArgumentException(
                    $annotation,
                    'class',
                    $class
                );
            }

            if (!isset($annotation->field)) {
                throw new MissingAnnotationArgumentException(
                    $annotation,
                    'field',
                    $class
                );
            }

            $children[$annotation->class] = $annotation->field;
        }

        return $children;
    }

}
