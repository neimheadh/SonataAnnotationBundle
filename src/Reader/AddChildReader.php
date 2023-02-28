<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Reader;

use Neimheadh\SonataAnnotationBundle\Annotation\AddChild;
use Neimheadh\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use ReflectionClass;

/**
 * AddChild annotation reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class AddChildReader extends AbstractReader
{

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

        foreach (
            $this->getClassAnnotations(
                $class,
                AddChild::class
            ) as $annotation
        ) {
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
