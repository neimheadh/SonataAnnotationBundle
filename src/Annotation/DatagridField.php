<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Annotation;

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
class DatagridField extends AbstractField
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
     * Datagrid field position.
     *
     * @var int
     */
    public int $position;

    /**
     * Get field settings.
     *
     * @return array
     */
    public function getSettings(): array
    {
        return [
            $this->type ?? null,
            $this->filterOptions,
            $this->fieldOptions,
            $this->fieldDescriptionOptions,
        ];
    }

}
