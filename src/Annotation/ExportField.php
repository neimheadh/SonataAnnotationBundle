<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

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
class ExportField implements AnnotationInterface
{
    /**
     * Export label.
     *
     * @var string
     */
    public string $label;
}
