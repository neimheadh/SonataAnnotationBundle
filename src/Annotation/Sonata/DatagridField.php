<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractField;
use Neimheadh\SonataAnnotationBundle\Annotation\PositionAnnotationInterface;
use ReflectionException;

/**
 * Datagrid field annotation.
 *
 * Allow you to configure the datagrid for the annotated field.
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class DatagridField extends AbstractField implements PositionAnnotationInterface
{

    /**
     * Filtering options.
     *
     * @var array
     */
    public array $filterOptions = [];

    /**
     * Datagrid form field type options.
     *
     * @var array
     */
    public array $fieldOptions = [];

    /**
     * @param string|array|null $type                    Type or annotation
     *                                                   parameters.
     * @param array             $fieldDescriptionOptions Description options.
     * @param int|null          $position                Position.
     * @param array             $filterOptions           Filtering options.
     * @param array             $fieldOptions            Datagrid form field
     *                                                   type options.
     *
     * @throws ReflectionException
     */
    public function __construct(
        $type = null,
        array $fieldDescriptionOptions = [],
        ?int $position = null,
        array $filterOptions = [],
        array $fieldOptions = []
    ) {
        $this->filterOptions = $filterOptions;
        $this->fieldOptions = $fieldOptions;

        parent::__construct($type, $fieldDescriptionOptions, $position);
    }

    /**
     * Get field settings.
     *
     * @return array
     */
    public function getSettings(): array
    {
        return [
            $this->type,
            $this->filterOptions,
            $this->fieldOptions,
            $this->fieldDescriptionOptions,
        ];
    }

}
