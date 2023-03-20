<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use Attribute;

/**
 * Show association field annotation.
 *
 * Allows you to configure your show field having an association field.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class ShowAssociationField extends ShowField implements
    AssociationFieldInterface
{

    /**
     * Association field name.
     *
     * @var string
     */
    public string $field;

}
