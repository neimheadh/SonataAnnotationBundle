<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AssociationFieldInterface;

/**
 * Export association field annotation.
 *
 * Allow you to configure the export for the annotated field having an
 * association field..
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class ExportAssociationField extends ExportField implements
    AssociationFieldInterface
{

    /**
     * Association field name.
     *
     * @var string|null
     */
    public ?string $field = null;

    /**
     * {@inheritDoc}
     *
     * @param string|null $field Field name.
     */
    public function __construct(
        $label = null,
        string $field = null
    ) {
        $this->field = $field;
        parent::__construct($label);
    }

}
