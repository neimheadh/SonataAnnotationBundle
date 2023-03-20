<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use Attribute;

/**
 * Show field annotation.
 *
 * Allows you to configure your show field.
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class ShowField extends AbstractField implements PositionAnnotationInterface
{
    /**
     * Field position.
     *
     * @var int
     */
    public int $position;
}
