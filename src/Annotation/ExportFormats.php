<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use Attribute;

/**
 * Export formats annotation.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class ExportFormats implements AnnotationInterface
{
    /**
     * Exported formats.
     *
     * @var array
     */
    public array $formats = [];
}
