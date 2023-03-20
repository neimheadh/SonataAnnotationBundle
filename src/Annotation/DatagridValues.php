<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Annotation;

use Attribute;

/**
 * Datagrid values annotation.
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class DatagridValues implements AnnotationInterface
{
    /**
     * Datagrid select values.
     *
     * @var array
     */
    public array $values = [];
}
