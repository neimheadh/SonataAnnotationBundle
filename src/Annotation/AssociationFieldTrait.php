<?php

namespace Neimheadh\SonataAnnotationBundle\Annotation;

/**
 * Association field trait.
 */
trait AssociationFieldTrait
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
     * @param string|null $field Association field name.
     */
    public function __construct(
        $type = null,
        array $fieldDescriptionOptions = [],
        ?int $position = null,
        ?string $field = null
    ) {
        $this->field = $field;

        parent::__construct($type, $fieldDescriptionOptions, $position);
    }

}