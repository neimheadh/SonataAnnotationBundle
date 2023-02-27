<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

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
class ShowField extends AbstractField implements PositionAnnotationInterface
{
    /**
     * Field position.
     *
     * @var int
     */
    public int $position;
}
