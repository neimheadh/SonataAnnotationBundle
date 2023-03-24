<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation\Sonata;

use Attribute;
use Neimheadh\SonataAnnotationBundle\Annotation\AbstractAnnotation;
use Neimheadh\SonataAnnotationBundle\Annotation\ArrayAnnotationTrait;
use ReflectionException;

/**
 * Datagrid values annotation.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @property array $values
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class DatagridValues extends AbstractAnnotation
{

    use ArrayAnnotationTrait;

    /**
     * @param array|string|null $values Datagrid values or annotation
     *                                  parameters.
     *
     * @throws ReflectionException
     */
    public function __construct(
        $values = []
    ) {
        $this->setArrayProperty('values', $values);
    }

}
