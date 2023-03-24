<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use ReflectionException;

/**
 * Field annotations main class.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
abstract class AbstractField extends AbstractAnnotation
{

    /**
     * Field type.
     *
     * @var string|null
     */
    public ?string $type = null;

    /**
     * Field description options.
     *
     * @var array
     */
    public array $fieldDescriptionOptions = [];

    /**
     * Field position.
     *
     * @var int|null
     */
    public ?int $position = null;

    /**
     * @param string|array|null $type                    Type or annotation
     *                                                   parameters.
     * @param array             $fieldDescriptionOptions Description options.
     * @param int|null          $position                Position.
     *
     * @throws ReflectionException
     */
    public function __construct(
        $type = null,
        array $fieldDescriptionOptions = [],
        ?int $position = null
    ) {
        $this->fieldDescriptionOptions = $fieldDescriptionOptions;
        $this->position = $position;

        if (is_array($type)) {
            $this->initAnnotation($type);
        } else {
            $this->type = $type;
        }
    }

    /**
     * Get field settings.
     *
     * @return array
     */
    public function getSettings(): array
    {
        return [
            $this->type ?? null,
            $this->fieldDescriptionOptions,
        ];
    }

}
