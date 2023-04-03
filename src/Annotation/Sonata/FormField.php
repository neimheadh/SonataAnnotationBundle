<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractField;
use Neimheadh\SonataAnnotationBundle\Annotation\ActionAnnotationInterface;
use Neimheadh\SonataAnnotationBundle\Annotation\PositionAnnotationInterface;
use ReflectionException;

/**
 * Form field annotation.
 *
 * Allows you to configure form field for the annotated property.
 *
 * @Annotation
 * @Target({"ANNOTATION", "PROPERTY"})
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
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
     * @var string|null
     */
    public ?string $action = null;

    /**
     * Field options.
     *
     * @var array
     */
    public array $options = [];

    /**
     * {@inheritDoc}
     *
     * @param string|null $action  Action name.
     * @param array       $options Field options.
     *
     * @throws ReflectionException
     */
    public function __construct(
        $type = null,
        array $fieldDescriptionOptions = [],
        ?int $position = null,
        ?string $action = null,
        array $options = []
    ) {
        $this->action = $action;
        $this->options = $options;

        parent::__construct($type, $fieldDescriptionOptions, $position);
    }

    /**
     * Get field form settings.
     *
     * @return array
     */
    public function getSettings(): array
    {
        return [
            $this->type,
            $this->options,
            $this->fieldDescriptionOptions,
        ];
    }

}
