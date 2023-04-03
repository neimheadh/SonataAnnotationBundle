<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractField;
use Neimheadh\SonataAnnotationBundle\Annotation\PositionAnnotationInterface;

/**
 * List field annotation.
 *
 * Allows you to configure your list field.
 *
 * @Annotation
 * @Target({"ANNOTATION", "PROPERTY", "METHOD"})
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class ListField extends AbstractField implements PositionAnnotationInterface
{

}
