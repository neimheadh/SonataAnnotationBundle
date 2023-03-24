<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractAnnotation;
use Neimheadh\SonataAnnotationBundle\Annotation\ArrayAnnotationTrait;
use ReflectionException;

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
final class ExportFormats extends AbstractAnnotation
{

    use ArrayAnnotationTrait;

    /**
     * Exported formats.
     *
     * @var array
     */
    private array $formats = [];

    /**
     * @param array|string|null $formats Exported formats or annotation
     *                                   parameters.
     *
     * @throws ReflectionException
     */
    public function __construct(
        $formats = []
    ) {
        $this->setArrayProperty('formats', $formats);
    }

}
