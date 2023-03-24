<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractAnnotation;
use ReflectionException;

/**
 * Export field annotation.
 *
 * Allow you to configure the export for the annotated field.
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY)]
class ExportField extends AbstractAnnotation
{

    /**
     * Export label.
     *
     * @var string|null
     */
    public ?string $label = null;

    /**
     * @param string|array|null $label Export label or annotation parameters.
     *
     * @throws ReflectionException
     */
    public function __construct(
        $label = null
    ) {
        if (is_array($label)) {
            $this->initAnnotation($label);
        } else {
            $this->label = $label;
        }
    }

}
