<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AssociationFieldInterface;
use ReflectionException;

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
     * @param array             $filterOptions           Filtering options.
     * @param array             $fieldOptions            Datagrid form field
     *                                                   type options.
     * @param string|null       $field                   Associated field name
     *
     * @throws ReflectionException
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
