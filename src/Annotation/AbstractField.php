<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Annotation;

/**
 * Field annotations main class.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
abstract class AbstractField implements AnnotationInterface
{
    /**
     * @var string
     */
    public string $type;

    /**
     * @var array
     */
    public array $fieldDescriptionOptions = [];

    /**
     * Get field settings.
     *
     * @return array
     */
    public function getSettings(): array
    {
        return [
            $this->type ?? null,
            $this->fieldDescriptionOptions
        ];
    }
}
