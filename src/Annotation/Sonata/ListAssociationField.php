<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AssociationFieldInterface;

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
     * @var string|null
     */
    public ?string $field = null;

    /**
     * @param string|array|null $type                    Type or annotation
     *                                                   parameters.
     * @param array             $fieldDescriptionOptions Description options.
     * @param int|null          $position                Position.
     * @param string|null       $field                   Association field name.
     *
     * @throws \ReflectionException
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
