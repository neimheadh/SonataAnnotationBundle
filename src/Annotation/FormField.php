<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

/**
 * Form field annotation.
 *
 * Allows you to configure form field for the annotated property.
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class FormField extends AbstractField implements
    ActionAnnotationInterface,
    PositionAnnotationInterface
{

    /**
     * Create action name.
     */
    public const ACTION_CREATE = 'create';

    /**
     * Edit action name.
     */
    public const ACTION_EDIT = 'edit';

    /**
     * Action name.
     *
     * @var string
     */
    public string $action;

    /**
     * Field options.
     *
     * @var array
     */
    public array $options = [];

    /**
     * Field position.
     *
     * @var int
     */
    public int $position;

    /**
     * Get field form settings.
     *
     * @return array
     */
    public function getSettings(): array
    {
        return [
            $this->type ?? null,
            $this->options,
            $this->fieldDescriptionOptions,
        ];
    }

}
