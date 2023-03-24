<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AssociationFieldInterface;
use Neimheadh\SonataAnnotationBundle\Annotation\AssociationFieldTrait;

/**
 * Datagrid association field annotation.
 *
 * Allow you to configure the datagrid for the annotated field having
 * an association field.
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class DatagridAssociationField extends DatagridField implements
    AssociationFieldInterface
{

    use AssociationFieldTrait;

    /**
     * {@inheritDoc}
     *
     * @param string|null $field Association field name.
     */
    public function __construct(
        $type = null,
        array $fieldDescriptionOptions = [],
        ?int $position = null,
        array $filterOptions = [],
        array $fieldOptions = [],
        ?string $field = null
    ) {
        $this->field = $field;

        parent::__construct(
            $type,
            $fieldDescriptionOptions,
            $position,
            $filterOptions,
            $fieldOptions
        );
    }

}
