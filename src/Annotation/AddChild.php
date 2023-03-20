<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use Attribute;

/**
 * Add child annotation.
 *
 * Add child admin to current admin class or admin annotated model.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AddChild implements AnnotationInterface
{
    /**
     * Child model class.
     *
     * @var string
     */
    public string $class;

    /**
     * Reverse field.
     *
     * @var string
     */
    public string $field;
}
