<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AssociationFieldInterface;
use Neimheadh\SonataAnnotationBundle\Annotation\AssociationFieldTrait;

/**
 * List association field annotation.
 *
 * Allows you to configure your list field having an association field.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class ListAssociationField extends ListField implements
    AssociationFieldInterface
{

    use AssociationFieldTrait;
}
