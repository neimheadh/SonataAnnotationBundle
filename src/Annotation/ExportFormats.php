<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Annotation;

/**
 * Export formats annotation.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class ExportFormats implements AnnotationInterface
{
    /**
     * Exported formats.
     *
     * @var array
     */
    public array $formats = [];
}
