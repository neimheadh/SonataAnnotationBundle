<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Annotation;

/**
 * List field annotation.
 *
 * Allows you to configure your list field.
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
class ListField extends AbstractField implements PositionAnnotationInterface
{

    /**
     * Field position.
     *
     * @var int
     */
    public int $position;

}
