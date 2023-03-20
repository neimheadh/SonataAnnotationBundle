<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use Attribute;

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

    /**
     * Association field name.
     *
     * @var string
     */
    public string $field;

}
